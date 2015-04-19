module.exports = function (grunt) {

    // This banner gets inserted at the top of the generated files, such a minified CSS
    var bannerContent = '/*!\n' +
        ' * <%= pkg.name %>\n' +
        ' * Version: <%= pkg.version %>\n' +
        ' * Build date: <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %>\n' +
        ' */\n\n';

    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jshint: {
            files: ['_js/main.js'],
            options: {
                forin: true,
                noarg: true,
                noempty: true,
                eqeqeq: true,
                bitwise: true,
                undef: false,
                unused: false,
                curly: true,
                browser: true,
                devel: true,
                jquery: true,
                indent: false,
                maxerr: 25
            }
        },
        concat: {
            options: {
                banner: bannerContent
            },
            javascript: {
                src: [
                    '_js/plugins.js',
                    '_js/main.js',
                    '_js/validation.js'
                ],
                dest: 'web/assets/js/all.js'
            }
        },
        uglify: {
            build: {
                files: {
                    'web/assets/js/all.min.js': ['web/assets/js/all.js']
                }
            }
        },
        watch: {
            js: {
                files: ['_js/*.js'],
                tasks: ['newer:jshint', 'newer:concat', 'uglify']
            }
        }
    });

    // Default task(s).
    grunt.registerTask('default', ['newer:jshint', 'newer:concat', 'newer:uglify']);
};