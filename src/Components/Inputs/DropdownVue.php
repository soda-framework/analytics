<?php

    namespace Soda\Analytics\Components\Inputs;

    use Soda\Cms\InterfaceBuilder\Forms\AbstractFormField;

    class DropdownVue extends AbstractFormField {

        protected $view = 'dropdown-vue';

        public function boot() {
            $this->setViewPath('soda-analytics::cms.partials.inputs');
        }

        public function getDefaultParameters() {
            return [
                'options' => [],
            ];
        }
    }
