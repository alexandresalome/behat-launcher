blApp.filter('duration', function ($translate) {
    return function (sec) {
        var res = '';
        if (sec > 3600) {
            var hourCount = Math.floor(sec/3600);
            res += $translate.instant('interval.hour', {count: hourCount});
            sec -= hourCount * 3600;

            if (sec > 0) {
                res += ', ';
            }
        }

        if (sec > 60) {
            var minuteCount = Math.floor(sec/60);
            res += $translate.instant('interval.minute', {count: minuteCount});
            sec -= minuteCount * 60;

            if (sec > 0) {
                res += ', ';
            }
        }


        if (sec > 0) {
            res += $translate.instant('interval.second', {count: sec});
        }

        return res;
    };
});
