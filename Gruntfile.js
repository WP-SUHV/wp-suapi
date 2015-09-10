/* jshint node:true */
module.exports = function (grunt) {
  'use strict';

  grunt.initConfig({
    // setting folder templates
    dirs: {
      css: 'assets/css',
      scss: 'assets/css/sass',
      js: 'assets/js'
    },

    pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        stripBanners: true,
        banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
        ' * <%= pkg.homepage %>\n' +
        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
        ' */\n'
      },
      main: {
        src: [
          '<%= dirs.js %>/src/wp-suapi.js'
        ],
        dest: '<%= dirs.js %>/wp-suapi.js'
      }
    },
    jshint: {
      all: [
        'Gruntfile.js',
        '<%= dirs.js %>/src/*.js',
        '<%= dirs.js %>/test/*.js'
      ]
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
        ' * <%= pkg.homepage %>\n' +
        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
        ' */\n',
        mangle: {
          except: ['jQuery']
        },
        preserveComments: 'some'
      },
      jsfiles: {
        files: [{
          expand: true,
          cwd: '<%= dirs.js %>/src',
          src: [
            '*.js',
            '!*.min.js',
            '!Gruntfile.js',
          ],
          dest: '<%= dirs.js %>/',
          ext: '.min.js'
        }]
      }
    },

    sass: {
      options: {
        precision: 2,
        sourceMap: true
      },
      dist: {
        files: {
          '<%= dirs.css %>/wp-suapi-admin.css': '<%= dirs.scss %>/wp-suapi-admin.scss',
          '<%= dirs.css %>/wp-suapi-frontend.css': '<%= dirs.scss %>/wp-suapi-frontend.scss'
        }
      }
    },

    postcss: {
      dist: {
        options: {
          processors: [
            require('autoprefixer-core')({browsers: 'last 2 versions'})
          ]
        },
        src: '<%= dirs.css %>/*.css'
      }
    },

    cssmin: {
      options: {
        banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
        ' * <%=pkg.homepage %>\n' +
        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
        ' */\n',
        processImport: false
      },
      minify: {
        expand: true,
        cwd: '<%= dirs.css %>/',
        src: ['*.css'],
        dest: '<%= dirs.css %>/',
        ext: '.min.css'
      }
    },
    watch: {
      livereload: {
        files: ['<%= dirs.css %>/*.css'],
        options: {
          livereload: true
        }
      },
      styles: {
        files: ['<%= dirs.css %>/sass/**/*.scss'],
        tasks: ['sass', 'autoprefixer', 'cssmin'],
        options: {
          debounceDelay: 500
        }
      },
      scripts: {
        files: ['<%= dirs.js %>/src/**/*.js', '<%= dirs.js %>/vendor/**/*.js'],
        tasks: ['jshint', 'concat', 'uglify'],
        options: {
          debounceDelay: 500
        }
      }
    },
    clean: {
      main: ['release/<%= pkg.version %>']
    },
    copy: {
      main: {
        files: [{
          expand: true,
          src: [
            '**',
            '!**/.*',
            '!**/readme.md',
            '!node_modules/**',
            '!vendor/**',
            '!tests/**',
            '!release/**',
            '!<%= dirs.css %>/sass/**',
            '!<%= dirs.js %>/src/**',
            '!images/src/**',
            '!bower.json',
            '!composer.json',
            '!composer.lock',
            '!Gruntfile.js',
            '!package.json',
            '!phpunit.xml',
            '!phpunit.xml.dist'
          ],
          dest: 'release/<%= pkg.version %>/'
        }]
      }
    },
    compress: {
      main: {
        options: {
          mode: 'zip',
          archive: './release/suapi.<%= pkg.version %>.zip'
        },
        expand: true,
        cwd: 'release/<%= pkg.version %>/',
        src: ['**/*'],
        dest: 'suapi/'
      }
    },
    wp_readme_to_markdown: {
      readme: {
        files: {
          'readme.md': 'readme.txt'
        }
      }
    },
    phpunit: {
      classes: {
        dir: ''
      },
      options: {
        bin: 'vendor/bin/phpunit',
        configuration: 'phpunit.xml',
        testsuite: 'unit'
      }
    },
  });

  // Load tasks
  require('load-grunt-tasks')(grunt);

  // Register tasks

  grunt.registerTask('css', ['sass', 'postcss', 'cssmin']);

  grunt.registerTask('js', ['jshint', 'concat', 'uglify']);

  grunt.registerTask('default', ['css', 'js', 'wp_readme_to_markdown']);

  grunt.registerTask('build', ['default', 'clean', 'copy', 'compress']);

  grunt.registerTask('test', ['phpunit']);

  grunt.util.linefeed = '\n';

};
