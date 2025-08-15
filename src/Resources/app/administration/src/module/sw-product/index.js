import './page/sw-product-detail';
import './page/sw-product-detail-notes';

const { Module } = Shopware;

// Extend the existing sw-product module with our notes route
Module.register('sw-product', {
    routes: {
        detail: {
            children: {
                notes: {
                    component: 'sw-product-detail-notes',
                    path: 'notes',
                    meta: {
                        parentPath: 'sw.product.index',
                        privilege: 'product.viewer',
                    },
                },
            },
        },
    },
}); 