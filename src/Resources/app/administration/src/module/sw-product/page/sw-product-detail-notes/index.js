import template from './sw-product-detail-notes.html.twig';

const { Context, Component, Mixin, Filter } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('sw-product-detail-notes', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            notes: [],
            isLoading: false,
            showModal: false,
            currentNote: null,
            showDeleteModal: false,
            noteToDelete: null
        };
    },

    computed: {
        productId() {
            return this.$route.params.id;
        },

        dateFilter() {
            return Filter.getByName('date');
        },

        noteRepository() {
            return this.repositoryFactory.create('academy_product_note');
        },

        noteColumns() {
            return [
                {
                    property: 'note',
                    label: this.$t('academy-product-notes.detail.columnNote'),
                    rawData: true
                },
                {
                    property: 'solved',
                    label: this.$t('academy-product-notes.detail.columnSolved'),
                    rawData: true
                },
                {
                    property: 'createdAt',
                    label: this.$t('academy-product-notes.detail.columnCreatedAt'),
                    rawData: true
                }
            ];
        }
    },

    created() {
        this.loadNotes();
    },

    methods: {
        loadNotes() {
            this.isLoading = true;
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('productId', this.productId));
            criteria.addSorting(Criteria.sort('createdAt', 'DESC'));

            this.noteRepository.search(criteria, Context.api)
                .then((result) => {
                    this.notes = result;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onAddNote() {
            this.currentNote = this.noteRepository.create(Context.api);
            this.currentNote.productId = this.productId;
            this.currentNote.solved = false;
            this.showModal = true;
        },

        onEditNote(note) {
            this.currentNote = note;
            this.showModal = true;
        },

        onDeleteNote(note) {
            this.noteToDelete = note;
            this.showDeleteModal = true;
        },

        onConfirmDelete() {
            if (!this.noteToDelete) {
                return;
            }

            this.noteRepository.delete(this.noteToDelete.id, Context.api)
                .then(() => {
                    this.createNotificationSuccess({
                        message: this.$t('academy-product-notes.detail.messageDeleteSuccess')
                    });
                    this.loadNotes();
                })
                .catch(() => {
                    this.createNotificationError({
                        message: this.$t('academy-product-notes.detail.messageError')
                    });
                })
                .finally(() => {
                    this.showDeleteModal = false;
                    this.noteToDelete = null;
                });
        },

        onCancelDelete() {
            this.showDeleteModal = false;
            this.noteToDelete = null;
        },

        onSaveNote() {
            this.noteRepository.save(this.currentNote, Context.api)
                .then(() => {
                    this.createNotificationSuccess({
                        message: this.$t('academy-product-notes.detail.messageSaveSuccess')
                    });
                    this.showModal = false;
                    this.loadNotes();
                })
                .catch(() => {
                    this.createNotificationError({
                        message: this.$t('academy-product-notes.detail.messageError')
                    });
                });
        },

        onCloseModal() {
            this.showModal = false;
            this.currentNote = null;
        }
    }
}); 