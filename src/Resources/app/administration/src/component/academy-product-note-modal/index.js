import template from './academy-product-note-modal.html.twig';

const { Component } = Shopware;

Component.register('academy-product-note-modal', {
    template,

    props: {
        note: {
            type: Object,
            required: true
        }
    },

    computed: {
        modalTitle() {
            return this.note.isNew() 
                ? this.$tc('academy-product-notes.detail.buttonAddNote')
                : this.$tc('academy-product-notes.detail.buttonEdit');
        }
    },

    methods: {
        onSave() {
            this.$emit('save');
        },

        onClose() {
            this.$emit('close');
        }
    }
}); 