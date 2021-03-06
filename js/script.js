var ftt = {
	spinOpts : {
		lines : 13, // The number of lines to draw
		length : 20, // The length of each line
		width : 10, // The line thickness
		radius : 30, // The radius of the inner circle
		corners : 1, // Corner roundness (0..1)
		rotate : 0, // The rotation offset
		direction : 1, // 1: clockwise, -1: counterclockwise
		color : '#3FABE9', // #rgb or #rrggbb
		speed : 1, // Rounds per second
		trail : 60, // Afterglow percentage
		shadow : false, // Whether to render a shadow
		hwaccel : false, // Whether to use hardware acceleration
		className : 'spinner', // The CSS class to assign to the spinner
		zIndex : 2e9, // The z-index (defaults to 2000000000)
		top : 'auto', // Top position relative to parent in px
		left : 'auto' // Left position relative to parent in px
	},

	init : function() {
		// implement nice maxlength plugin
		$('input[maxlength]').maxlength();

		// SCT First button actions
		$('#searchBtn').on('click', function() {
			$('#ICPC-Code').hide();
			$('#conceptValidation').hide();
			$('#ActionButtons').hide();
			$('#conceptsDropdown')[0].selectedIndex = 0;
			$('#conceptsDropdown').unbind('change');
			$('dl.synonyms, #icpcDropdown, #icpcClearBtn').hide();
			ftt.concepts.refine();
		});
		$('#clearBtn').on('click', function() {
			ftt.concepts.reset();
		});
		$('#icpcSearchBtn').on('click', function() {
			$('span.icpcSelected').empty();
			$('#icpcSelectedDiv').hide();
			$('#icpcDropdown')[0].selectedIndex = 0;
			$('#icpcDropdown').unbind('change');
			ftt.icpccodes.refine();
		});
		$('#icpcClearBtn').on('click', function() {
			ftt.icpccodes.reset();
		});

		// ICPC First button actions
		$('#icpcSearchBtn2').on('click', function() {
			$('#SCT-Code').hide();
			$('#ActionButtons').hide();
			$('#icpcDropdown')[0].selectedIndex = 0;
			$('#icpcDropdown').unbind('change');
			//alert('clicked button');
			ftt.icpccodesFirst.refine();
		});
		$('#icpcClearBtn2').on('click', function() {
			ftt.icpccodesFirst.reset();
		});

		$('#nextBtn').on('click', function(e) {
			e.preventDefault();
			$(this).closest('form').attr('action', 'add-hi.php').submit();
		});
		$('#finishedBtn').on('click', function(e) {
			e.preventDefault();
			$(this).closest('form').attr('action', 'review-encounter.php').submit();
		});
		$('.deleteItemBtn').on('click', function(e) {
			e.preventDefault();
			var $this = $(e.target);
			var wId = $this.attr('id').split('-')[1];
			var numThis = $this.closest('form').find('#numThis').val();
			var thisType = ($this.closest('form').find('#itemType').val().toString() === "0" ? "RFE" : "Health Issue");
			if (numThis < 2) {
				bootbox.alert('Each encounter must have at least one RFE and at least one Health Issue.');
			} else {
				bootbox.confirm("Delete " + thisType + " #" + wId + " - Are you sure?", function(result) {
					if (result) {
						// TODO: show some kind of spinner (spin.js) while this call is made
						$.ajax({
							url : 'deleteItem.php',
							type : 'POST',
							data : 'id=' + wId,
							success : function(response, textStatus, jqXHR) {
								bootbox.alert(thisType + " #" + wId + " successfully deleted.", function() {
									window.location.href = "review-encounter.php?cancel=1";
								})
							},
							error : function(jqXHR, textStatus, errorThrown) {
								alert('error: ' + errorThrown);
							}
						});
					}
				});
			}
		});
		$('.deleteEncounterBtn').on('click', function(e) {
			e.preventDefault();
			var $this = $(e.target);
			var wId = $this.attr('id').split('-')[1];
			var wLabel = $this.attr('id').split('-')[2];
			bootbox.confirm("Delete Encounter #" + wLabel + " - Are you sure?", function(result) {
				if (result) {
					// TODO: show some kind of spinner (spin.js) while this call is made
					$.ajax({
						url : 'deleteEncounter.php',
						type : 'POST',
						data : 'id=' + wId,
						success : function(response, textStatus, jqXHR) {
							bootbox.alert("Encounter #" + wLabel + " successfully deleted.", function() {
								window.location.href = "index.php";
							});
						},
						error : function(jqXHR, textStatus, errorThrown) {
							alert('error: ' + errorThrown);
						}
					});
				}
			});
		});

		// setup js spinners to display before AJAX calls in itemsHolder on encounters list page
		$('.itemsHolder .spin').each(function() {
			var $this = $(this);
			var spinner = new Spinner(ftt.spinOpts).spin($this[0]);
		});

		// AJAX calls for on encounters list page
		$('.encounters-list .collapse').not('fetched').on('shown', function() {
			var $this = $(this), $container = $this.find('.itemsHolder'), wId = $container.attr('id').split('-')[1];

			$.ajax({
				url : 'getItems.php',
				type : 'POST',
				data : 'enc=' + wId,
				dataType : 'json',
				success : function(response, textStatus, jqXHR) {
					$this.addClass('fetched');
					var insertHtml = ftt.items.write(response);
					$container.empty().append(insertHtml);
				},
				error : function(jqXHR, textStatus, errorThrown) {
					alert('error: ' + errorThrown);
				}
			});
		});

		// scrollto and open encounter after label change
		if (($('.encounters-list').length > 0) && (window.location.search.indexOf('scrollTo') !== -1)) {
			var wId = window.location.search.split('scrollTo=')[1];
			var wX = $('#collapse' + wId).closest('.accordion-group').offset().top;
			window.scrollTo(0, wX);
			$('#collapse' + wId).parent().find('.accordion-toggle').trigger('click');
		}

		/* trap pressing RETURN on add/edit form - send focus to search box
		$('form#addItem, form#editItem').bind('keypress', function(e) {
		if (e.keyCode === 13) {
		e.preventDefault();
		$('#searchBox').focus();
		}
		}); */

		// if already in SCT search box, RETURN triggers search
		$('#searchBox').bind('keypress', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				$('#searchBtn').click();
				$('#searchBox').focus();
			}
		});
		// if already in ICPC search box, RETURN triggers search
		$('#icpcSearchBox').bind('keypress', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				$('#icpcSearchBtn').click();
				$('#icpcSearchBox').focus();
			}
		});

		// if already in ICPC2 search box, RETURN triggers search
		$('#icpcSearchBox2').bind('keypress', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				$('#icpcSearchBtn2').click();
				$('#icpcSearchBox2').focus();
			}
		});

		// any keypress in alternative box triggers fields to be displayed
		$('#conceptFreeText').bind('keypress', function(e) {
			var altText = $('#conceptFreeText').val();
			var icpcCode = $('#icpc2').val();
			if (altText != '') {
				$('#ICPC-Code').show();
				if (icpcCode == '') {
					$('#ActionButtons').hide();
				} else {
					$('#ActionButtons').show();
				}
			}
		});
	},

	// icpcCode specific searches and result displays
	icpccodes : {
		refine : function() {
			var searchText = $('#icpcSearchBox').val();

			if (searchText == '') {
				$('#icpcDropdown, #icpcClearBtn').hide();
				$('span.icpcSelected').empty();
				$('#icpcSelectedDiv').hide();
				$('#icpcDropdown')[0].selectedIndex = 0;
				$('#icpcDropdown').unbind('change');
			} else {
				$.ajax({
					url : 'searchICPC.php',
					type : 'POST',
					data : 'searchText=' + searchText,
					dataType : 'json',
					success : function(response, textStatus, jqXHR) {
						tools.rewriteICPCDropdown($('#icpcDropdown'), response);

						if (response.length > 0) {
							$('#icpcValidation').hide();
							$('#icpcDropdown, #icpcClearBtn').show();
							$('#icpcDropdown').on('change', function() {
								var cid = $('#icpcDropdown').val();
								if (cid != '') {
									$('#icpcSelectedDiv').show();
									var str = $('#icpcDropdown option:selected').text();
									$('span.icpcSelected').empty().append(str);
									$('#icpcDropdown').focus();
									$('#ActionButtons').show();
								} else {
									$('span.icpcSelected').empty();
									$('#icpcSelectedDiv').hide();
									$('#ActionButtons').hide();
								}
							});
						} else {
							$('#icpcDropdown, #icpcClearBtn').hide();
							$('#icpcValidation').show();
							if ($('#icpc2').val() == '') {
								$('#ActionButtons').hide();
							}
						}

					},
					error : function(jqXHR, textStatus, errorThrown) {
						alert('error: ' + errorThrown);
					}
				});
			}
		},

		reset : function() {
			$('#icpcSearchBox').val('');
			ftt.icpccodes.refine();
			$('span.selectedICPC').empty().append('');
			$('dl.selectedICPCDL').hide();
		},
	},

	// SCT concept specific searches and result displays
	concepts : {
		refine : function() {
			var searchText = $('#searchBox').val();
			var altText = $('#conceptFreeText').val();
			//			alert('searchText - ' + searchText + ' and altText - ' + altText);

			if ((searchText == '') && (altText == '')) {
				$('#ICPC-Code').hide();
				$('#conceptValidation').hide();
				$('#ActionButtons').hide();
				$('#conceptsDropdown')[0].selectedIndex = 0;
				$('#conceptsDropdown').unbind('change');
				$('#conceptsDropdown, dl.synonyms, #clearBtn, #icpcDropdown, #icpcClearBtn, dl.selectedConceptDL').hide();
			} else {
				var refid = $('#refType').val();

				$.ajax({
					url : 'searchConcepts.php',
					type : 'POST',
					data : {
						searchText : searchText,
						refid : refid
					},
					dataType : 'json',
					success : function(response, textStatus, jqXHR) {
						//						$('#conceptsDropdown').empty();
						tools.rewriteDropdown($('#conceptsDropdown'), response);

						if (response.length > 0) {
							$('#conceptValidation').hide();
							$('#conceptsDropdown, #clearBtn').show();
						} else {
							$('#conceptsDropdown, #clearBtn').hide();
							$('#conceptValidation').show();
							$('#ActionButtons').hide();
							$('#ICPC-Code, dl.synonyms, dl.selectedConceptDL').hide();
//							var str = "No match";
							$('span.icpcCode').empty().append('');
							$('#icpc2').val('');
						}

						$('#conceptsDropdown').on('change', function() {
							var cid = $('#conceptsDropdown').val();
							var optid = $('#option').val();

							//							alert('cid on the dropdown is - ' + cid);
							//							alert('option is is - ' + optid);

							if (cid != '') {
								$('#ActionButtons').show();
								$('dl.selectedConceptDL').show();
								var str = cid + ' - ' + $("#conceptsDropdown option:selected").text();
								$('span.selectedConcept').empty().append(str);
								ftt.concepts.getSynonyms();
								if (optid == 1) {
									ftt.concepts.getICPC();
								}
							} else {
								var str = "No match";
								$('span.selectedConcept').empty().append('');
								$('span.selectedICPC').empty().append('');
								$('span.icpcCode').empty().append('');
								$('#icpc2').val('');
								$('#ICPC-Code, dl.synonyms, dl.selectedConceptDL').hide();
								$('#ActionButtons').hide();
							}
						});
						$('#icpcDropdown', '#icpcClearBtn').hide();
					},
					error : function(jqXHR, textStatus, errorThrown) {
						alert('error: ' + errorThrown);
					}
				});
			}
		},

		reset : function() {
			$('#searchBox').val('');
			$('#conceptFreeText').val('');
			$('#icpcSearchBox').val('');
//			var str = "No match";
			$('span.icpcCode').empty().append('');
			$('#icpc2').val('');
			$('#conceptValidation').hide();
			$('#icpcValidation').hide();
			ftt.concepts.refine();
		},
		getSynonyms : function() {
			var cid = $('#conceptsDropdown').val();

			$.ajax({
				url : 'getSynonyms.php',
				type : 'POST',
				data : 'syn=' + cid,
				dataType : 'json',
				success : function(response, textStatus, jqXHR) {
					ftt.concepts.showSynonyms(response);
				},
				error : function(jqXHR, textStatus, errorThrown) {
					alert('error: ' + errorThrown);
				}
			});
		},
		showSynonyms : function(data) {
			var str = '<ul>';
			for (var x = 0; x < data.length; x++) {
				str += '<li>' + data[x].synonym + '</li>';
			}
			str += '</ul>';
			$('#itemsHolder').hide();
			$('dl.synonyms dd').empty().append(str);
			$('dl.synonyms').show();
			$('#ICPC-Code').show();
		},

		getICPC : function() {
			var codeid = $('#conceptsDropdown').val();

			$.ajax({
				url : 'getICPC.php',
				type : 'POST',
				data : 'codeid=' + codeid,
				dataType : 'json',
				success : function(response, textStatus, jqXHR) {
					if (response.length > 0) {
						ftt.concepts.showICPC(response);
						ftt.icpccodes.reset();
						$('#ActionButtons').hide();
						$('#icpcCodeDropdown').on('change', function() {
							$('#ActionButtons').show();
							$('dl.selectedICPCDL').show();
							$('span.selectedICPC').empty().append($("#icpcCodeDropdown option:selected").text());
							var icpcid = $('#icpcCodeDropdown').val();
							$('#icpc2').val(icpcid);
						});
						
					} else {
						$('#ActionButtons').hide();
						var str = "No match";
						$('dl.selectedICPCDL').show();
						$('span.selectedICPC').empty().append(str);
						$('#icpc2').val('');
					}
				},
				error : function(jqXHR, textStatus, errorThrown) {
					alert('error: getICPC - ' + errorThrown + ' ' + textStatus + ' ' + codeid);
				}
			});
		},

		showICPC : function(data) {
			var $obj = $('#icpcCodeDropdown');
			$obj.empty();
			var id;
			var str;
			for (var x = 0; x < data.length; x++) {
				id = data[x].id;
				str = id + " - " + data[x].title;
				if (id.indexOf("UNMCH") !== -1) {
					str = data[x].title;
				}
				$obj.append('<option value="' + id + '">' + str + '</option>');
				$('#icpc2').val(data[x].id);
			}

		},
	},

	// the following functions cover the ICPC first option
	// icpcCode specific searches and result displays

	icpccodesFirst : {
		refine : function() {
			var searchText = $('#icpcSearchBox2').val();
			//alert("search box val is - " + searchText);

			if (searchText == '') {
				$('#SCT-Code').hide();
				$('#ActionButtons').hide();
				$('#icpcDropdown')[0].selectedIndex = 0;
				$('#icpcDropdown').unbind('change');
				$('#icpcDropdown, #icpcClearBtn2, dl.selectedICPCDL').hide();
			} else {
				$.ajax({
					url : 'searchICPC.php',
					type : 'POST',
					data : 'searchText=' + searchText,
					dataType : 'json',
					success : function(response, textStatus, jqXHR) {
						tools.rewriteICPCDropdown($('#icpcDropdown'), response);

						if (response.length > 0) {
							$('#icpcValidation').hide();
							$('#icpcDropdown, #icpcClearBtn2').show();
							$('#icpcDropdown').on('change', function() {
								$('dl.selectedICPCDL').show();
								$('span.selectedICPC').empty().append($("#icpcDropdown option:selected").text());
								ftt.icpccodesFirst.getSCTMap();
								$('#ActionButtons').hide();
							});
						} else {
							$('#icpcValidation').show();
							$('#icpcDropdown, #icpcClearBtn2').hide();
							$('#ActionButtons').hide();
							$('#conceptFreeText').val('');
							$('#conceptValidation').hide();
							$('#SCT-Code, dl.synonyms').hide();
							$('dl.selectedICPCDL').hide();
							$('span.selectedICPC').empty().append('');

							/*							var str = "No match";
							 $('span.icpcCode').empty().append(str);
							 $('#icpc2').val('');
							 */
						}

					},
					error : function(jqXHR, textStatus, errorThrown) {
						alert('error: ' + errorThrown);
					}
				});
			}
		},

		reset : function() {
			$('#icpcSearchBox2').val('');
			$('#conceptFreeText').val('');
			$('#conceptValidation').hide();
			$('#icpcValidation').hide();
			ftt.icpccodesFirst.refine();
		},

		getSCTMap : function() {
			var codeid = $('#icpcDropdown').val();
			var refid = $('#refType').val();

			$.ajax({
				url : 'getSCTMap.php',
				type : 'POST',
				data : {
					codeid : codeid,
					refid : refid
				},
				dataType : 'json',
				success : function(response, textStatus, jqXHR) {
					$('#SCT-Code').show();
					tools.rewriteDropdown($('#conceptsDropdown'), response);

					if (response.length > 0) {
						$('#conceptValidation').hide();
						$('#conceptsDropdown, #icpcClearBtn2').show();
						$('#conceptsDropdown').on('change', function() {
							var cid = $('#conceptsDropdown').val();
							if (cid != '') {
								$('dl.selectedConceptDL').show();
								$('span.selectedConcept').empty().append($("#conceptsDropdown option:selected").text());
								ftt.concepts.getSynonyms();
								$('#ActionButtons').show();
							} else {
								$('dl.synonyms, dl.selectedConceptDL').hide();
								$('#ActionButtons').hide();
								$('span.selectedConcept').empty().append('');

							}
						});
					} else {
						$('#conceptValidation').show();
						$('#conceptsDropdown').hide();
						$('dl.synonyms, dl.selectedConceptDL').hide();
						$('#ActionButtons').hide();
						$('span.selectedConcept').empty().append('');
					}

				},
				error : function(jqXHR, textStatus, errorThrown) {
					alert('error: getSCTMap - ' + errorThrown + ' ' + textStatus + ' ' + codeid);
				}
			});
		},

		showSCT : function(data) {
			var str = '<ul>';

			for (var x = 0; x < data.length; x++) {
				str += '<li>' + data[x].id + ' - ' + data[x].title + '</li>';
			}
			str += '</ul>';
			$('dl.sct-mapped-concepts dd').empty().append(str);
		},
	},

	items : {
		write : function(data) {
			tools.dir(data);
			var str = '<dl class="dl-horizontal"><dt>Reason For Encounters</dt><dd><ul>';
			for (var x = 0; x < data.length; x++) {// RFEs only
				if (data[x].type === "0") {
					str += '<li>' + data[x].term + '</li>';
				}
			}
			str += '</ul></dd><dt>Health Issues</dt><dd><ul>';
			for (var y = 0; y < data.length; y++) {// HIs only
				if (data[y].type === "1") {
					str += '<li>' + data[y].term + '</li>';
				}
			}
			str += '</ul></dd></dl>';

			return str;
		}
	}
};

var tools = {
	// safe logging
	log : function(msg) {
		if (window && window.console) {
			console.log(msg);
		}
	},
	dir : function(obj) {
		if (window && window.console) {
			console.dir(obj);
		}
	},

	// SCT concept dropdown rebuilding from JSON
	rewriteDropdown : function($obj, data) {
		var $tmp = $obj.find('option')[0];
		if (data.length == 0) {
			$tmp = "<option value=''>Select SCT Concept</option>";
		}
		$obj.empty().append($tmp);
		for (var x = 0; x < data.length; x++) {
			$obj.append('<option value="' + data[x].conceptId + '">' + data[x].term + '</option>');
		}
	},

	// ICPC Code dropdown rebuilding from JSON
	rewriteICPCDropdown : function($obj, data) {
		var $tmp = $obj.find('option')[0];
		$obj.empty().append($tmp);
		for (var x = 0; x < data.length; x++) {
			$obj.append('<option value="' + data[x].id + '">' + data[x].id + ' - ' + data[x].title + '</option>');
		}
	}
};

$(function() {
	ftt.init();
});
