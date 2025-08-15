import template from './sw-product-detail.html.twig';

const { Component } = Shopware;

Component.override('sw-product-detail', {
    template,

    computed: {
        productCriteria() {
            const criteria = this.$super('productCriteria');
            criteria.addAssociation('academyProductNotes');
            return criteria;
        }
    }
}); 