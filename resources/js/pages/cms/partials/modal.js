if ($('#analytics-error').length) {
    window.analytics_error = new Vue({
        el: '#analytics-error',
        data: {
            title: '',
            text: '',
        },
        methods: {
            error: function(title, text){
                var me = this;

                me.title = title;
                me.text = text;

                $('#analytics-error').modal('show');
            },
            close: function () {
                var me = this;

                me.title = '';
                me.text = '';

                $('#analytics-error').modal('hide');
            }
        },
    });
}
