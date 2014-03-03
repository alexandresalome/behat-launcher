module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            recess: { files: ['assets/**/*.less'], tasks: ['recess'], options: { spawn: false } },
            uglify: { files: ['assets/**/*.js'],     tasks: ['uglify'], options: { spawn: false } }
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
            build: {
                dest: 'web/js/all.min.js',
                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/bootstrap/dist/js/bootstrap.js',
                    'bower_components/angular/angular.js',
                    'bower_components/angular-translate/angular-translate.js',
                    'bower_components/angular-translate-loader-url/angular-translate-loader-url.js',
                    'bower_components/angular-translate-interpolation-messageformat/angular-translate-interpolation-messageformat.js',
                    'bower_components/angular-route/angular-route.js',
                    'bower_components/angular-resource/angular-resource.js',
                    'assets/js/main.js',
                    'assets/js/app.js'
                ]
            }
        },

        recess: {
            build: {
                options: { compile: true, compress: true },
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.css',
                    'assets/less/main.less',
                ],
                dest: 'web/css/all.min.css'
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
