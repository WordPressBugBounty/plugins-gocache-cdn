module.exports = {
	dist : [
		'concat', 'sass:dist', 'imagemin'
	],
	dev : [
		'concat', 'sass:dev', 'handlebars:dist'
	]
};