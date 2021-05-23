var config = {
	paths: {
		"inputslider" : "Netbaseteam_Themeconfig/js/inputslider-0.3.min",
		"materialize" : "Netbaseteam_Themeconfig/js/materialize.min",
		"spectrum" : "Netbaseteam_Themeconfig/js/spectrum/js/spectrum",
		"select2" : "Netbaseteam_Themeconfig/js/select2/js/select2.min"
	},
	shim: {
		'inputslider': {
                deps: ['jquery'] //gives your parent dependencies name here
            },
            'materialize': {
                deps: ['jquery'] //gives your parent dependencies name here
            },
            'spectrum': {
                deps: ['jquery'] //gives your parent dependencies name here
            },
            'select2': {
                deps: ['jquery'] //gives your parent dependencies name here
            }
        }

    };