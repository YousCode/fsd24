<template>
    <div
        class="block welcome-card__block has-bg"
        :style="HelloCardImg"
        style="background-repeat: no-repeat;
    background-size: cover;"
    >
        <div class="block__inner">
            <div class="d-flex justify-content-between">
                <p class="date c-grey"
                style="text-transform: none;"
                >Pour vous,</p>
                <p
                    class="welcome-card__time"
                    style="background: rgba(0, 0, 0, 0.5);"
                >
                    {{ time }}
                </p>
            </div>
            <h2 class="big-title">Aujourd’hui</h2>
            <p class="text" 
            style="padding-top: 5px; text-transform: capitalize
            ;"
            >{{ date }}.</p>
        </div>
        <div class="block__bck-image"></div>
    </div>
</template>

<script>
export default {
    props: ["user", "HelloCardImg"],

    data() {
        return {
            date: this.getDate(),
            time: this.updateTime(),
            api_key: "23404a14679c252e2aea08bdd76a7b04",
            url_base: "https://api.openweathermap.org/data/2.5/",
            location: "Paris",
            weather: {}
        };
    },

    methods: {
        updateTime() {
            this.date = this.getDate();
            this.time = this.getTime();
            this.scheduleUpdateTime();
        },
        scheduleUpdateTime() {
            setTimeout(this.updateTime, 1000);
        },

        getDate() {
            const options = {
                weekday: "long",
                month: "long",
                day: "numeric"
            };
            return new Date().toLocaleDateString("fr-FR", options);
        },
        getTime() {
            var hours = new Date()
                .getHours()
                .toString()
                .padStart(2, "0");
            var minutes = new Date()
                .getMinutes()
                .toString()
                .padStart(2, "0");

            return hours + ":" + minutes;
        }
    }
};
</script>
