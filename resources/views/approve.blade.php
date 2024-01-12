<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('/images/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ url('/images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>KOLAB - Admin</title>
</head>

<body style="background-color: rgb(14, 6, 35);">
    <div class="container" style="display: grid;height: 100%;place-items: center;display: flex;justify-content: center;"
        data-bs-theme="dark">
        <div class="row justify-content-center">
            <ul class="nav nav-tabs mb-3" id="tab-admin" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="admin-tab-users-to-approve" href="#admin-users-to-approve"
                        data-mdb-toggle="tab" role="tab" aria-controls="admin-users-to-approve"
                        aria-selected="true">
                        Utilisateurs à approuver
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="admin-tab-explorer-client-to-approve"
                        href="#admin-explorer-client-to-approve" data-mdb-toggle="tab" role="tab"
                        aria-controls="admin-explorer-client-to-approve" aria-selected="false">
                        Utilisateurs EXPLORER (CLIENT) à approuver
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="admin-tab-explorer-freelance-to-approve"
                        href="#admin-explorer-freelance-to-approve" data-mdb-toggle="tab" role="tab"
                        aria-controls="admin-explorer-freelance-to-approve" aria-selected="false">
                        Utilisateurs EXPLORER (FREELANCE) à approuver
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="admin-tab-explorer-users-to-certify"
                        href="#admin-explorer-users-to-certify" data-mdb-toggle="tab" role="tab"
                        aria-controls="admin-explorer-users-to-certify" aria-selected="false">
                        Utilisateurs EXPLORER à certifier
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="admin-tab-explorer-access-code" href="#admin-explorer-access-code"
                        data-mdb-toggle="tab" role="tab" aria-controls="admin-explorer-access-code"
                        aria-selected="false">
                        Code d'accès EXPLORER
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="admin-tab-workspaces" href="#admin-workspaces" data-mdb-toggle="tab"
                        role="tab" aria-controls="admin-workspaces" aria-selected="false">
                        Gestion des espaces de travail
                    </a>
                </li>
            </ul>
            <!-- ADMIN NAVS -->

            <div class="row">
                 @if (session('message'))
                    <div class="alert alert-success @error('approve') is-invalid field-error @enderror" unique role="alert">
                         {{ session('message') }}
                     </div>
                @endif
            </div>

            <!-- ADMIN CONTENT -->
            <div class="tab-content" id="content-admin">
                <div class="tab-pane fade show active" id="admin-users-to-approve" role="tabpanel"
                    aria-labelledby="admin-users-to-approve">
                    <div class="card-body">
                        <table class="table" style="margin-bottom: 30px">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Téléphone</th>
                                <th>Profil</th>
                                <th>E-mail</th>
                                <th>Enregistré le</th>
                                <th>Espace de travail</th>
                                <th>Statut</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @forelse ($approve as $approvals)
                                <tr>
                                    <td>{{ $approvals->lastname }}</td>
                                    <td>{{ $approvals->firstname }}</td>
                                    <td>{{ $approvals->phone }}</td>
                                    <td class="approve">{{ $approvals->contact_status }}</td>
                                    <td>{{ $approvals->email }}</td>
                                    @error('approve')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <td>{{ $approvals->created_at }}</td>
                                    <td>{{ $approvals->workspace }}</td>
                                    <td>
                                        @if($approvals->status == 'waiting')
                                            <button class="btn btn-primary">En attente</button>
                                        @endif
                                        @if($approvals->status == 'accepted')
                                        <button class="btn btn-success">Accepté</button>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('unlockAccess', $approvals->id) }}" class="btn btn-outline-success">Approuver</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('DeniedAccess', $approvals->id) }}" class="btn btn-outline-danger">Refuser</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Pas d'utilisateurs à approuver.</td>
                                </tr>
                            @endforelse

                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="admin-explorer-client-to-approve" role="tabpanel"
                    aria-labelledby="admin-explorer-client-to-approve">
                    <table class="table" style="margin-bottom: 30px">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>E-mail</th>
                            <th>Statut</th>
                            <th>Type de compte</th>
                            <th>Description du projet</th>
                            <th>Budget</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @forelse ($explorerRegistration as $registrations)
                            @if ($registrations->registration_type == \App\Enum\ExplorerRegistrationTypeEnum::CLIENT)
                                <tr>
                                    <td>{{ $registrations->user_id }}</td>
                                    <td>{{ $registrations->lastname }}</td>
                                    <td>{{ $registrations->firstname }}</td>
                                    <td>{{ $registrations->email }}</td>
                                    <td>
                                        @if($registrations->status == 'waiting')
                                            <button class="btn btn-primary">En attente</button>
                                        @endif
                                        @if($registrations->status == 'accepted')
                                            <button class="btn btn-success">Accepté</button>
                                        @endif
                                    </td>
                                    <td>{{ $registrations->registration_type }}</td>
                                    <td class="approve">{{ $registrations->project_description }}</td>
                                    <td>{{ $registrations->budget }}</td>
                                        @error('registrations')
                                            <span class="invalid-feedback error-message" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    <td>
                                        <a href="{{ route('clientAccessExplorer', $registrations->id) }}" class="btn btn-outline-success">Approuver</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('clientDeniedExplorer', $registrations->id) }}" class="btn btn-outline-danger">Refuser</a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4">Pas d'utilisateurs à approuver.</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div class="tab-pane fade" id="admin-explorer-freelance-to-approve" role="tabpanel"
                    aria-labelledby="admin-explorer-client-to-approve">
                    <table class="table">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>E-mail</th>
                            <th>Phone</th>
                            <th>Portfolio</th>
                            <th>Métier</th>
                            <th>Compétence(s)</th>
                            <th>Statut</th>
                            <th>Type de compte</th>
                            <th>Description du projet</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @forelse ($explorerRegistration as $registrations)
                            @if ($registrations->registration_type == \App\Enum\ExplorerRegistrationTypeEnum::FREELANCE)
                                <tr>
                                    <td>{{ $registrations->user_id }}</td>
                                    <td>{{ $registrations->lastname }}</td>
                                    <td>{{ $registrations->firstname }}</td>
                                    <td>{{ $registrations->email }}</td>
                                    <td>{{ $registrations->phone }}</td>
                                    <td>{{ $registrations->portfolio }}</td>
                                    <td>{{ $registrations->jobs }}</td>
                                    <td>{{ $registrations->skills }}</td>
                                    <td>
                                        @if($registrations->status == 'waiting')
                                            <button class="btn btn-primary">En attente</button>
                                        @endif
                                        @if($registrations->status == 'accepted')
                                            <button class="btn btn-success">Accepté</button>
                                        @endif
                                    </td>
                                    <td>{{ $registrations->registration_type }}</td>
                                    <td class="approve">{{ $registrations->project_description }}</td>
                                    @error('registrations')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <td>
                                        <a href="{{ route('talentAccessExplorer', $registrations->id) }}" class="btn btn-outline-success">Approuver</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('talentDeniedExplorer', $registrations->id) }}" class="btn btn-outline-danger">Refuser</a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4">Pas d'utilisateurs à approuver</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div class="tab-pane fade" id="admin-explorer-users-to-certify" role="tabpanel"
                    aria-labelledby="admin-explorer-users-to-certify">
                    <table class="table" style="margin-bottom: 30px">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>E-mail</th>
                            <th>Téléphone</th>
                            <th>Certification</th>
                        </tr>
                        @forelse ($certified as $certify)
                            <tr>
                                <td>{{ $certify->id }}</td>
                                <td>{{ $certify->lastname }}</td>
                                <td>{{ $certify->firstname }}</td>
                                <td>{{ $certify->email }}</td>
                                <td>{{ $certify->phone }}</td>
                                <td class="text-center">
                                    <label class="switch">
                                        <input type="checkbox" name="certified" value="{{ $certify->certified }}"
                                            {{ $certify->certified ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Pas d'utilisateurs à certifier.</td>
                            </tr>
                        @endforelse

                    </table>
                </div>
                <div class="tab-pane fade" id="admin-explorer-access-code" role="tabpanel"
                    aria-labelledby="admin-explorer-access-code">
                    <table class="table" style="margin-bottom: 30px">
                        {{ Form::open(['route' => 'user_explorer_code_generate']) }}
                        {{ Form::token() }}
                        {{ Form::submit('Générer le code') }}
                        {{ Form::close() }}
                        <tr>
                            <th>ID</th>
                            <th>Code d'accès</th>
                            <th>Nombre d'utilsation (pas utilisé)</th>
                            <th>Date d'expiration (pas utilisé)</th>
                            <th>Actif</th>
                            <th>Talents qui ont utilisé ce code</th>
                            <th>Suppression du code</th>
                        </tr>
                        @forelse ($explorerCodes as $explorerCode)
                            <tr>
                                <td>{{ $explorerCode->id }}</td>
                                <td>
                                    {{ Form::open(['route' => 'user_explorer_code_actions']) }}
                                    {{ Form::token() }}
                                    {{ Form::text('accessCode', $explorerCode->access_code) }}
                                    {{ Form::hidden('explorerCodeId', $explorerCode->id) }}
                                    {{ Form::hidden('action', 'UPDATE') }}
                                    {{ Form::submit('Mettre à jour le code') }}
                                    {{ Form::close() }}
                                </td>
                                <td>{{ $explorerCode->usage }}</td>
                                <td>{{ $explorerCode->expiration }}</td>
                                <td>
                                    {{ $explorerCode->is_active }}
                                    {{ Form::open(['route' => 'user_explorer_code_actions']) }}
                                    {{ Form::token() }}
                                    {{ Form::hidden('isActive', true) }}
                                    {{ Form::hidden('explorerCodeId', $explorerCode->id) }}
                                    {{ Form::hidden('action', 'UPDATE') }}
                                    {{ Form::submit('Activer/Désactiver le code') }}
                                    {{ Form::close() }}
                                </td>
                                <td>
                                    @if (!empty($explorerCode->users))
                                        @foreach ($explorerCode->users as $user)
                                            {{ $user->firstname }} {{ $user->lastname }} //
                                        @endforeach
                                    @else
                                        Code non utilisé
                                    @endif
                                </td>
                                <td>
                                    {{ Form::open(['route' => 'user_explorer_code_actions']) }}
                                    {{ Form::token() }}
                                    {{ Form::hidden('explorerCodeId', $explorerCode->id) }}
                                    {{ Form::hidden('action', 'DELETE') }}
                                    {{ Form::submit('Supprimer le code') }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>Aucun code Explorer généré</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div class="tab-pane fade" id="admin-workspaces" role="tabpanel" aria-labelledby="admin-workspaces">
                    <table class="table" style="margin-bottom: 30px">
                        <tr>
                            <th>ID</th>
                            <th>Nom du Workspace</th>
                            <th>Propriétaire du Workspace</th>
                            <th>Membres du Workspace</th>
                            <th>Changer le propriétaire</th>
                            <th>Ajouter un Membre</th>
                        </tr>
                        @foreach ($workspaces as $workspace)
                            <tr>
                                <td>{{ $workspace->id }}</td>
                                <td>{{ $workspace->name }}</td>
                                <td>{{ $workspace->owner->firstname }} {{ $workspace->owner->lastname }}</td>
                                <td>
                                    @if ($workspace->members)
                                        @foreach ($workspace->members as $member)
                                            {{ $member->firstname }} {{ $member->lastname }} //
                                        @endforeach
                                    @else
                                        Pas de membre dans ce Workspace
                                    @endif
                                </td>
                                <td>
                                    {{ Form::open(['route' => 'workspace_update_owner']) }}
                                    {{ Form::token() }}
                                    {{ Form::hidden('workspaceId', $workspace->id) }}
                                    {{ Form::email('email', $workspace->owner->email) }}

                                    {{ Form::submit('Changer') }}
                                    {{ Form::close() }}
                                </td>
                                <td>
                                    {{ Form::open(['route' => 'workspace_add_member']) }}
                                    {{ Form::token() }}
                                    {{ Form::hidden('workspaceId', $workspace->id) }}
                                    {{ Form::text('email') }}

                                    {{ Form::submit('Ajouter') }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <!-- ADMIN CONTENT -->
        </div>
    </div>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>
