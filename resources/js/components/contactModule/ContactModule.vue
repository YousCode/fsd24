<template>
    <div class="contact-module" @mouseenter="show = true" @mouseleave="show = false">
        <img src="/images/contact-img.svg" alt="icone de contacte" />
        <transition name="open">
            <div v-show="show" id="expand">
                <span >Besoin d’aide ? <a :href="'tel:06 99 08 88 94'" class="number">06 99 08 88 94</a></span>
            </div>
        </transition>
    </div>
</template>
<script>
export default {
    data() {
        return {
            show: false
        };
    },
    methods: {
        toggle() {
            this.show = !this.show;
        }
    }
};
</script>
