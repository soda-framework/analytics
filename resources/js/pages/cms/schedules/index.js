if ($('#schedules').length) {
    var analytics_vm = new Vue({
        el: '#schedules',
        data: {
        },
        mounted: function () {
            var me = this;

            $('body').on('change','select[name=schedule_frequency]',function(){
                $('form#schedule_frequency').submit();
            });
        },
        methods: {

        },
    });
}
