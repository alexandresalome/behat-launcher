module.exports = function(grunt) {

    var stylesheetFiles = [
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'assets/less/navbar.less',
        'assets/less/main.less',
    ];

    var javascriptFiles = [
        'bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',
        'bower_components/angular/angular.js',
        'bower_components/angular-sanitize/angular-sanitize.js',
        'bower_components/angular-translate/angular-translate.js',
        'bower_components/angular-translate-loader-url/angular-translate-loader-url.js',
        'bower_components/angular-translate-interpolation-messageformat/angular-translate-interpolation-messageformat.js',
        'bower_components/angular-route/angular-route.js',
        'bower_components/angular-resource/angular-resource.js',
        'bower_components/angular-local-storage/angular-local-storage.js',
        'bower_components/messageformat/messageformat.js',
        'bower_components/messageformat/locale/fr.js',
        'bower_components/messageformat/locale/en.js',
        'assets/js/debt.js',
        'assets/js/app.js',
        'assets/js/controller/*.js',
        'assets/js/directive/*.js',
        'assets/js/factory/*.js',
        'assets/js/filter/*.js'
    ];

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            recess: { files: ['assets/**/*.less'], tasks: ['recess:cat', 'recess:min'] },
            uglify: { files: ['assets/**/*.js'],     tasks: ['uglify:cat', 'uglify:min'] }
        },

        copy: {
            build: {
                expand: true,
                dest: 'web/fonts',
                src: '*',
                cwd: 'bower_components/bootstrap/dist/fonts'
            }
        },

        uglify: {
            min: {
                options: { mangle: false },
                dest: 'web/js/all.min.js',
                src: javascriptFiles
            },
            cat: {
                options: { mangle: false, compress: false, beautify: true },
                dest: 'web/js/all.js',
                src: javascriptFiles
            }
        },

        recess: {
            min: {
                options: { compile: true, compress: true },
                src: stylesheetFiles,
                dest: 'web/css/all.min.css'
            },
            cat: {
                options: { compile: true, compress: false },
                src: stylesheetFiles,
                dest: 'web/css/all.css'
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-recess');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');

    // Default task(s).
    grunt.registerTask('default', ['uglify', 'recess', 'copy']);

};
