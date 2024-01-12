<template>
    <!-- IF NO PROJECT -->
    <div>
        <div class="block__inner">
            <div class="projects-table__content">
                <div class="project-card__col">
                    <div class="project-card is-waiting">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="project-card__actions-buttons js-actions-buttons"
                            >
                                <button class="action-button js-action-button">
                                    Accepter
                                </button>
                                <button class="action-button js-action-button">
                                    Décliner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            :class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                    backgroundColor:
                                  project.category.bgColor,
                                  border:
                                  '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
                <div class="project-card__col">
                    <div class="project-card">
                        <div
                            class="project__category"
                            :style="
                                project.category.bgColor &&
                                project.category.borderColor
                                    ? {
                                          backgroundColor:
                                              project.category.bgColor,
                                          border:
                                              '1.42px solid ' +
                                              project.category.borderColor,
                                      }
                                    : {}
                            "
                            v-if="project.category"
                        >
                            Catégorie
                        </div>
                        <div class="project-card__infos">
                            <p><strong>Tâche</strong></p>
                            <p>Nom du projet</p>
                        </div>
                        <div class="text-center">
                            <button class="action-button">Clôturer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="no-content__block">
            <div class="no-content__inner">
                <div class="text-center">
                    <span class="picto picto-task"></span>
                </div>
                <h2 class="main-title">Pas de tâche en cours</h2>
                <p class="text c-main-grey">
                    Les tâches en cours s'afficheront ici
                </p>
            </div>
        </div>
    </div>
    <!-- ENDIF NO PROJECT -->
</template>

<script>
export default {
    props: [],

    data() {
        return {};
    },

    mounted() {
        this.setCategoriesSelect();
    },

    methods: {},
};
</script>
