<template>
    <div :class="'w-100'+(errors.length ? ' has-error' : '')">
        <textarea v-if="type === 'textarea'" @focusout="$emit('triggerValidation')" :placeholder="placeholder" v-model="content" @input="$emit('input', content)" v-bind="htmlAttributes"/>
        <input v-else :type="type" class="form-field" @focusout="$emit('triggerValidation')" :placeholder="placeholder" v-model="content" @input="$emit('input', content)" v-bind="htmlAttributes"/>
        <div v-if="errors.length" class="errors_message">
          <div v-for="(error, i) in errors" :key="i">
            {{ error }}
          </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'text'
        },
        placeholder: {
            type: String,
            default: '',
        },
        value: '',
        errors: {
            type: Array,
            default: () => {return []}
        },
        classes: {
            type: String,
            default: null,
        },
        min: {
            type: String,
            default: null,
        },
        max: {
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        required: {
            type: Boolean,
            default: false
        },
        maxlength: {
            type: String,
        },
        rows: {
            type: String,
        },
        cols: {
            type: String,
        }
    },
    computed: {
        htmlAttributes() {
            const attrs = {};

            if (this.disabled) {
                attrs.disabled = 'disabled';
            }
            if (null !== this.class) {
                attrs.class = this.classes;
            }
            if (null !== this.min) {
                attrs.min = this.min;
            }
            if (null !== this.max) {
                attrs.max = this.max;
            }
            if (null !== this.maxlength) {
                attrs.maxlength = this.maxlength;
            }
            if (null !== this.rows) {
                attrs.rows = this.rows;
            }
            if (null !== this.cols) {
                attrs.cols = this.cols;
            }
            if (this.required) {
                attrs.required = 'required';
            }
            return attrs;
        },
    },
    data() {
        return {
            content:this.value,
        }
    },
}
</script>

<style lang="scss" scoped>
    .has-error {
        .errors_message {
            color: #580619;
            font-size: 0.95rem;
            margin: 5px 0px -0.95rem 10px;
        }

        input {
            border: 1px solid #580619 !important;
        }
    }

    input[type=time] {
        text-align: center;

        &::-webkit-calendar-picker-indicator {
            display:none;
        }
    }

    textarea {
        font-size: 1.6rem;
    }
</style>