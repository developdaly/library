'use strict';
module.exports = function (grunt) {
	
	require('time-grunt')(grunt);

	var phpSource = [ '**/*.php' ];
	var phpExceptions = [ '!vendor/**/*.php', '!node_modules/**/*.php' ];
	
	grunt.initConfig({

		phplint: {
			options: {
				phpCmd: "/usr/bin/php", // Or "c:\EasyPHP-5.3.8.1\PHP.exe"
				phpArgs: {
					"-l": null
				},
				spawnLimit: 10
			},

			files: phpSource.concat(phpExceptions),
		},

		phpcbf: {
			options: {
				bin: 'vendor/bin/phpcbf',
				verbose: true
			},
			files: phpSource.concat(phpExceptions)
		},

		phpcs: {
			application: {
				src: phpSource.concat(phpExceptions)
			},
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'vendor/wp-coding-standards/wpcs/WordPress/ruleset.xml',
				verbose: true
			}
		},
		phpcpd: {
			theme: {
				dir: './'
			},
			options: {
				bin: './vendor/bin/phpcpd',
				quiet: false,
				names: "*.php",
				minLines: 3,
				minTokens: 70,
				verbose: true,
				exclude: 'vendor'
			}
		}

	});
	
	// LINT PHP
	grunt.registerTask('lint:php', [], function() {
		grunt.loadNpmTasks('grunt-phplint');
		grunt.loadNpmTasks('grunt-phpcbf');
		grunt.loadNpmTasks('grunt-phpcs');
		grunt.loadNpmTasks('grunt-phpcpd');
		grunt.task.run('phplint', 'phpcbf', 'phpcs', 'phpcpd');
	});
	
	grunt.registerTask('default', 'lint:php');

};
