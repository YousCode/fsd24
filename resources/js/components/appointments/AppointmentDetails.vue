<template>
    <div
        :class="`text details picto picto-card-${icon}`"
    >
        <a
            v-if="isLink && content"
            :href="`${linkType}:${content}`"
            class="content"
            >{{ content }}
        </a>
        <span v-else class="content">
            {{ content !== null ? content : 'Non renseigné' }}
        </span>
    </div>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            required: true,
        },
        linkType: {
            type: String, // tel, mail, location
            default: null,
        },
        content: {
            type: String,
            default: null,
        }
    },
    computed: {
        isLink() {
            if (this.linkType !== null) {
                return true;
            }

            return false;
        }
    }
}
</script>

<style lang="scss">
.details {
  background-color: hsl(255, 39%, 35%);
  display: flex;
  align-items: center;
  border-radius: 4px;
  padding: 14px;

  &:hover {
    a, a:hover {
        text-decoration: none;
        color: white;
    }
  }
}
</style>