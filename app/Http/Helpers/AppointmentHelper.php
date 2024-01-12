<?php

namespace App\Http\Helpers;

use App\Models\Appointment;
use App\Models\KolabContact;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class AppointmentHelper
 * @package App\Http\Helpers
 */
class AppointmentHelper
{

    /**
     * Get all appointments
     * @param $request
     * @return Appointment[]|Collection [Object]  Appointment
     */
    public function all($request = [])
    {
        $appointments = Appointment::query()->select(['*'])->get();

        return $appointments;
    }

    /**
     * Get appointment
     * @param  [Integer] $id Appointment id
     * @return Appointment|Builder|Model|object|null [Object]  Appointment
     */
    public function get($id)
    {
        return Appointment::where('id', $id)->with('talents')->first();
    }
    /**
     * Get appointment
     * @param  [Integer] $id Appointment id
     * @return Appointment|Builder|Model|object|null [Object]  Appointment
     */
    public function getUsers($id)
    {
        return User::where('user_id', $id)->first();
    }

    /**
     * Get appointment locations
     * @param  [Integer] $id Appointment id
     * @return Appointment|Builder|Model|object|null [Object]  Appointment
     */
    public function getLocation($request)
    {
        $locations = Appointment::where('user_id', $request['user_id'])->whereNotNull('location')->orderByDesc('created_at')->groupBy('location')->pluck('location')->toArray();

        $output = [];
        foreach ($locations as $location) {
            $loc = new \StdClass;
            $loc->name = $location;
            $output[] = $loc;
        }

        return $output;
    }

    /**
     * Add or update an appointment
     * @param
     * @return mixed [Object]  Appointment
     */
    public function addOrUpdate($params)
    {

        $appointmentParams = $params['appointment'];

        if (isset($appointmentParams['id']) && $appointmentParams['id']) {
            $appointment = Appointment::find($appointmentParams['id']);
            $appointment->updated_by = (int)$params['user'][0];
        } else {
            $appointment = new Appointment();
            $appointment->created_by = (int)$params['user'][0];
            $appointment->updated_by = (int)$params['user'][0];
        }

        $appointment->title = $appointmentParams['title'];
        $appointment->note = $appointmentParams['note'];
        $appointment->user_id = (int)$params['user'][0];


        $users = [];
        $emails = [];

        // For every talents added (a known user or just an email invited)
        foreach($appointmentParams['talents'] as $talent) {
            if($talent['id'] !== null) {
                $users[] = $talent['id'];
            } else {
                $emails[] = $talent;
            }
        }
        
        if(!in_array($appointment->user_id, $users)) {
            $users[] = $appointment->user_id;
        }

        $appointment->datetime = Carbon::parse($appointmentParams['datetime']);
        $appointment->location = $appointmentParams['location'];

        try {
            $appointment->save();
            if ([] !== $users) {
                $appointment->talents()->sync($users);
            }

            $contactsToSync = [];
            foreach($emails as $email) {
                $contact = KolabContact::where('email', $email['name'])->first();

                if(null === $contact) {
                    $newContact = new KolabContact(['email' => $email['name']]);
                    $newContact->save();
                    $contactsToSync[] = $newContact->id;
                } else {
                    $contactsToSync[] = $contact->id;
                }
            }

            if([] !== $contactsToSync) {
                $appointment->contacts()->sync($contactsToSync);
            }
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return Appointment::where('id', $appointment->id)->with('talents')->get()[0];
    }

    /**
     * Delete One appointment
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $appointment = Appointment::find($id);
        try {
            $appointment->delete();
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return true;
    }
}
