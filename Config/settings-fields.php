<?php

return [
  'form-emails' => [
    'name' => 'icommerce::form-emails',
    'value' => null,
    'type' => 'input',
    'group' => 'icommerce::common.pages.index',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.emails'
    ],
  ],
  'product-per-page' => [
    'name' => 'icommerce::product-per-page',
    'value' => 12,
    'group' => 'icommerce::common.pages.index',
    'type' => 'input',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.product-per-page'
    ],
  ],
  'customIndexTitle' => [
    'name' => 'icommerce::customIndexTitle',
    'value' => '',
    'group' => 'icommerce::common.pages.index',
    'type' => 'input',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.customIndexTitle'
    ],
  ],
  'carouselIndexCategory' => [
    'value' => 'carousel-category-active',
    'name' => 'icommerce::carouselIndexCategory',
    'group' => 'icommerce::common.pages.index',
    'type' => 'select',
    'props' => [
      'label' => 'Imagenes para el Carousel Top',
      'useInput' => false,
      'useChips' => false,
      'multiple' => false,
      'hideDropdownIcon' => true,
      'newValueMode' => 'add-unique',
      'options' => [
        ['label' => 'Categoria Activa','value' => 'carousel-category-active'],
        ['label' => 'Categoria Padre','value' => 'carousel-category-parent'],
      ]
    ]
  ],
  'showBreadcrumbSidebar' => [
    'value' => false,
    'name' => 'icommerce::showBreadcrumbSidebar',
    'type' => 'checkbox',
    'group' => 'icommerce::common.pages.index',
    'props' => [
      'label' => 'Mostrar breadcrumb en Sidebar'
    ]
  ],
  'filterCategoriesTitle' => [
    'value' => 'basic',
    'name' => 'icommerce::filterCategoriesTitle',
    'group' => 'icommerce::common.filters.categories.group',
    'type' => 'select',
    'props' => [
      'label' => 'Titulo a mostrar',
      'useInput' => false,
      'useChips' => false,
      'multiple' => false,
      'hideDropdownIcon' => true,
      'newValueMode' => 'add-unique',
      'options' => [
        ['label' => 'Palabra Categoria','value' => 'basic'],
        ['label' => 'Titulo de la Categoria','value' => 'category-title'],
      ]
    ]
  ],
  'filterRangePricesStep' => [
    'name' => 'icommerce::filterRangePricesStep',
    'group' => 'icommerce::common.filters.priceRange.group',
    'value' => 20000,
    'type' => 'input',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.filters.priceRange.step'
    ],
  ],

  'daysEnabledForNewProducts' => [
    'name' => 'icommerce::daysEnabledForNewProducts',
    'value' => 15,
    'type' => 'input',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.daysEnabledForNewProducts',
      'type' => 'number'
    ],
  ],
  'productListItemLayout' => [
    'value' => 'product-list-item-layout-1',
    'name' => 'icommerce::productListItemLayout',
    'group' => 'icommerce::common.settings.product.group',
    'type' => 'select',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.product.layout',
      'useInput' => false,
      'useChips' => false,
      'multiple' => false,
      'hideDropdownIcon' => true,
      'newValueMode' => 'add-unique',
      'options' => [
        ['label' => 'List Product Layout 1','value' => 'product-list-item-layout-1'],
        ['label' => 'List Product Layout 2','value' => 'product-list-item-layout-2'],
        ['label' => 'List Product Layout 3','value' => 'product-list-item-layout-3']
      ]
    ]
  ],
  'product-add-to-cart-with-quantity' => [
    'name' => 'icommerce::product-add-to-cart-with-quantity',
    'value' => "0",
    'type' => 'checkbox',
    'columns' => 'col-12 col-md-6',
    'group' => 'icommerce::common.settings.product.group',
    'props' => [
      'label' => 'icommerce::common.settings.product-add-to-cart-with-quantity',
      'trueValue' => "1",
      'falseValue' => "0",
    ],
  ],
  'product-price-list-enable' => [
      'name' => 'icommerce::product-price-list-enable',
      'value' => "0",
      'type' => 'checkbox',
      'columns' => 'col-12 col-md-6',
      'group' => 'icommerce::common.filters.priceList.group',
      'props' => [
          'label' => 'icommerce::common.settings.product-price-list-enable',
          'trueValue' => "1",
          'falseValue' => "0",
      ],
  ],
  'productMinimumQuantityToNotify' => [
    'name' => 'icommerce::productMinimumQuantityToNotify',
    'group' => 'icommerce::common.settings.product.group',
    'value' => 3,
    'type' => 'input',
    'columns' => 'col-12 col-md-6',
    'props' => [
      'label' => 'icommerce::common.settings.product.minimumQuantityToNotify'
    ],
  ],
];
