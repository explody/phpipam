yaml = require('js-yaml');
fs   = require('fs');

try {
  var doc = yaml.safeLoad(fs.readFileSync('config/config.yml', 'utf8'));
  var env = doc['environment'];
} catch (e) {
  console.log(e);
}

var copy_js = [
    { src: ['bower_components/jquery/dist/jquery.min.js'],                              dest: '<%= grunt.config("static_dir") %>/js/jquery.js' },
    { src: ['bower_components/select2/dist/js/select2.min.js'],                         dest: '<%= grunt.config("static_dir") %>/js/select2.js' },
    { src: ['bower_components/datatables/media/js/jquery.dataTables.min.js'],           dest: '<%= grunt.config("static_dir") %>/js/jquery.datatables.js' },
    { src: ['bower_components/StickyTableHeaders/js/jquery.stickytableheaders.min.js'], dest: '<%= grunt.config("static_dir") %>/js/jquery.stickytableheaders.js' },
    { src: ['bower_components/bootstrap/dist/js/bootstrap.min.js'],                     dest: '<%= grunt.config("static_dir") %>/js/bootstrap.js' },
    { src: ['bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js'],       dest: '<%= grunt.config("static_dir") %>/js/bootstrap.switch.js' },
    { src: ['bower_components/gmaps/gmaps.min.js'],                                     dest: '<%= grunt.config("static_dir") %>/js/gmaps.js' },
    { src: ['bower_components/moment/min/moment.min.js'],                               dest: '<%= grunt.config("static_dir") %>/js/moment.js' },
    { src: ['bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'], 
          dest: '<%= grunt.config("static_dir") %>/js/bootstrap.datetimepicker.js' },
    { src: ['bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js'], 
          dest: '<%= grunt.config("static_dir") %>/js/bootstrap.colorpicker.js' },
    { src: ['bower_components/Flot/excanvas.min.js'],                                   dest: '<%= grunt.config("static_dir") %>/js/excanvas.js' }
];

var copy_css = [
    { src: ['bower_components/bootstrap/dist/css/bootstrap.min.css'],                           dest: '<%= grunt.config("static_dir") %>/css/bootstrap.css' },
    { src: ['bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'],  dest: '<%= grunt.config("static_dir") %>/css/bootstrap.switch.css' },
    { src: ['bower_components/datatables/media/css/jquery.dataTables.min.css'],                 dest: '<%= grunt.config("static_dir") %>/css/jquery.datatables.css' },
    { src: ['bower_components/font-awesome/css/font-awesome.min.css'],                          dest: '<%= grunt.config("static_dir") %>/css/font-awesome.css' },
    { src: ['bower_components/select2/dist/css/select2.min.css'],                               dest: '<%= grunt.config("static_dir") %>/css/select2.css', },
    { src: ['bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'],       
          dest: '<%= grunt.config("static_dir") %>/css/bootstrap.datetimepicker.css' },
];

var uglify_bower = {
    '<%= grunt.config("static_dir") %>/js/bootstrap.slider.js':          ['bower_components/bootstrap-slider/bootstrap-slider.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.flot.js':               ['bower_components/Flot/jquery.flot.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.flot.pie.js':           ['bower_components/Flot/jquery.flot.pie.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.flot.categories.js':    ['bower_components/Flot/jquery.flot.categories.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.ui.widget.js':          ['bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.iframe-transport.js':   ['bower_components/blueimp-file-upload/js/jquery.iframe-transport.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.fileupload.js':         ['bower_components/blueimp-file-upload/js/jquery.fileupload.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.multi-select.js':       ['bower_components/multiselect/js/jquery.multi-select.js'],
    '<%= grunt.config("static_dir") %>/js/jquery.md5.js':                ['bower_components/jquery-md5/jquery.md5.js'],
};

var uglify_local = [
    { 
        '<%= grunt.config("static_dir") %>/js/jquery.ui.js': ['components/js/jquery-ui-1.10.4.custom.min.js'],
    },
    {
        expand: true,
        cwd: 'components/js',
        src: '**/*.js',
        dest: '<%= grunt.config("static_dir") %>/js'
    }
];

var cssmin_bower = {
    '<%= grunt.config("static_dir") %>/css/bootstrap.slider.css':         ['bower_components/bootstrap-slider/slider.css'],
    '<%= grunt.config("static_dir") %>/css/multi-select.css':             ['bower_components/multiselect/css/multi-select-fixed.css'],
    '<%= grunt.config("static_dir") %>/css/bootstrap.custom.css':         ['components/css/bootstrap-custom.css'],
};

module.exports = function(grunt) {

    grunt.initConfig({
        static_dir: "<%= pkg.config.static.dir %>/<%= pkg.version %>",
        static_path: "<%= pkg.config.static.path %>/<%= pkg.version %>",
        env: "<%= env %>"
    })

    var tasksConfig = {

        pkg: grunt.file.readJSON('package.json'),

        jshint: {
            reporter: require('jshint-stylish'),
            options: {
                // use jshint-stylish to make our errors look and read good
                curly: true,
                eqeqeq: true,
                eqnull: true,
                browser: true,
                globals: {
                    jQuery: true
                },
            },

            // when this task is run, lint the Gruntfile and all js files in src
            build: ['Gruntfile.js', 'js/<%= pkg.version %>/*.js']
        },

        // Some of these are already minified by the authors but I'm keeping them all here 
        // for the sake of organization.
        uglify: {
            options: {
                banner: '/*\n <%= pkg.name %> <%= pkg.version %> <%= grunt.template.today("yyyy-mm-dd-HH:MM:ss") %> \n*/\n',
            },
            bower_components: {
                files: uglify_bower
            },
            local_components: {
                options: {
                    beautify: true,
                    compress: {
                        unused: false,
                    },
                    mangle: false,
                },
                files: uglify_local
            }
        },

        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            bower_components: {
                files: cssmin_bower
            },
            local_components: {
                files: {
                    '<%= grunt.config("static_dir") %>/css/bootstrap.custom.css': [
                        'components/css/bootstrap-custom.css'
                    ]
                }
            }
        },
        
        // None of the url replacement options worked as hoped
        replace: {
            multiselect: {
                src: ['bower_components/multiselect/css/multi-select.css'],
                dest: 'bower_components/multiselect/css/multi-select-fixed.css',
                replacements: [{
                    from: /\.\.\/img/g,
                    to: "\.\.\/images"
                }]
            },
        },

        copy: {
            js: {
                files: [
                    copy_js,
                ]
            },
            css: {
                files: [
                    copy_css,
                ]
            },
            bs_fonts: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'bower_components/bootstrap',
                    dest: '<%= grunt.config("static_dir") %>',
                    src: ['fonts/*.*'],
                }]
            },
            fa_fonts: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'bower_components/font-awesome',
                    dest: '<%= grunt.config("static_dir") %>',
                    src: ['fonts/*.*'],
                }]
            },
            local_fonts: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'components',
                    dest: '<%= grunt.config("static_dir") %>',
                    src: ['fonts/*.*'],
                }]
            },
            local_image: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'components',
                    dest: '<%= grunt.config("static_dir") %>',
                    src: ['images/**/*'],
                }]
            },
            bcp: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'bower_components/bootstrap-colorpicker/dist/img',
                    dest: '<%= grunt.config("static_dir") %>/images',
                    src: ['bootstrap-colorpicker/*'],
                }]
            },
            datatables: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'bower_components/datatables/media',
                    dest: '<%= grunt.config("static_dir") %>',
                    src: ['images/*.*'],
                }]
            },
            multiselect: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'bower_components/multiselect/img',
                    dest: '<%= grunt.config("static_dir") %>/images/',
                    src: ['*'],
                }]
            },
        },

        less: {
            fa: {
                options: {
                    paths: ['bower_components/'],
                    plugins: [ new(require('less-plugin-clean-css'))() ],
                    modifyVars: {
                        'fa-font-path': "\"/<%= grunt.config('static_path') %>/fonts\""
                    },
                },
                files: {
                    '<%= grunt.config("static_dir") %>/css/font-awesome.css': 'bower_components/font-awesome/less/font-awesome.less',
                }
            },
            bcp: {
                options: {
                    paths: ['bower_components/'],
                    plugins: [ new(require('less-plugin-clean-css'))() ],
                    modifyVars: {
                        'imgPath': "\"/<%= grunt.config('static_path') %>/images/bootstrap-colorpicker/\""
                    },
                },
                files: {
                    '<%= grunt.config("static_dir") %>/css/bootstrap.colorpicker.css': 'bower_components/bootstrap-colorpicker/src/less/colorpicker.less',
                }
            },
            select2_bootstrap: {
                options: {
                    paths: ['bower_components/'],
                    plugins: [ new(require('less-plugin-clean-css'))() ],
                },
                files: {
                    '<%= grunt.config("static_dir") %>/css/select2.bootstrap.css': 'bower_components/select2-bootstrap-theme/src/build.less',
                }
            }
        },

        rsync: {
            options: {
                args: ["-azvHl"],
                exclude: [".git*","*.scss",".DS_Store"],
                recursive: true
            },
            ckeditor: {
                options: {
                    args: ["-azvHlq"],
                    src: "bower_components/ckeditor",
                    dest: '<%= grunt.config("static_dir") %>/',
                    exclude: ['.git','samples','skins/moono','skins/kama'],
                    delete: true,
                }
            },
        },
        
        exec: {
            db: {
                cmd: function() {
                    var phinx_cmds = ['breakpoint','create','migrate','status']
                    var pcmd = 'status';
                    var penv = env;
                    var runphinx = true;
                    // if the first arg is in the supported phinx_cmds
                    // then use the default environment
                    if (phinx_cmds.indexOf(arguments[0]) != -1) {
                        penv = env;
                        pcmd = arguments[0];
                    } else {
                        // Otherwise, the first argument after db: is presumed to be an environment name
                        penv = arguments[0];
                        pcmd = arguments[1];
                        // make sure the given command is in our supported list
                        if (phinx_cmds.indexOf(pcmd) == -1) {
                            throw grunt.util.error("Invalid phinx command: " + pcmd);
                        }
                    }

                    var runcmd = 'vendor/bin/phinx ' + pcmd + ' -c config/phinx.php -e ' + penv;
                    console.log("Running " + runcmd);
                    return runcmd;

                },
                exitCode: [0,1]
            }
        },

        watch: {
            scripts: {
                files: ['components/js/**/*.js'],
                tasks: ['uglify:local_components'],
                options: {
                    spawn: false,
                    interval: 2000,
                },
            },
            css: {
                files: ['components/css/**/*.css'],
                tasks: ['cssmin:local_components'],
                options: {
                    spawn: false,
                },
            },
            sync: {
                files: ['app/**/*.php',
                        'api/**/*.php',
                        'functions/**/*.php',
                        'js/**/*.js',
                        'css/**/*.css',
                        '*.php',
                        '*.js'],
                tasks: ['replace',
                        'uglify', 
                        'cssmin', 
                        'less', 
                        'copy', 
                        'rsync:ckeditor', 
                        ],
                options: {
                    spawn: false,
                },
            },
        },
        
    };

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks("grunt-rsync");
    grunt.loadNpmTasks('grunt-text-replace');
    grunt.loadNpmTasks('grunt-exec');
    
    grunt.config.merge(tasksConfig);

    grunt.registerTask('default', [
                                   'replace',
                                   'uglify', 
                                   'cssmin', 
                                   'less', 
                                   'copy', 
                                   'rsync:ckeditor'
                                  ]
                              );

};
