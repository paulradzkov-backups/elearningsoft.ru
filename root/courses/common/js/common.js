function jlms_courses_common_init() {
	var tipelems = $$('dfn[title], abbr[title], acronym[title]')
	var JTooltips = new Tips(tipelems, { maxTitleChars: 50, fixed: false}); 
	tipelems.addClass('pseudolink');
}