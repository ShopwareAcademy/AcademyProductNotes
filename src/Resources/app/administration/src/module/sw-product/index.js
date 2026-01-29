import './page/sw-product-detail';
import './page/sw-product-detail-notes';

const productModule = Shopware.Module.getModuleRegistry().get('sw-product');

if (productModule) {
    productModule.routes.get('sw.product.detail').children.push({
        name: 'sw.product.detail.productNotes',
        path: '/sw/product/detail/:id?/product-notes',
        component: 'sw-product-detail-notes',
        meta: {
            parentPath: 'sw.product.index',
            privilege: 'product.viewer',
        },
    });
}