<?php
/* ----------------------------------------------------------------------
 * themes/default/views/bundles/ca_objects_default_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2018 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
	$va_access_values = caGetUserAccessValues($this->request);
	$t_object = 			$this->getVar("item");
	$va_comments = 			$this->getVar("comments");
	$va_tags = 				$this->getVar("tags_array");
	$vn_comments_enabled = 	$this->getVar("commentsEnabled");
	$vn_share_enabled = 	$this->getVar("shareEnabled");
	$vn_pdf_enabled = 		$this->getVar("pdfEnabled");
	$vn_id =				$t_object->get('ca_objects.object_id');
	$vs_rep_viewer =		trim($this->getVar('representationViewer'));

	$o_icons_conf = caGetIconsConfig();
	$va_object_type_specific_icons = $o_icons_conf->getAssoc("placeholders");
	if(!($vs_default_placeholder = $o_icons_conf->get("placeholder_media_icon"))){
		$vs_default_placeholder = "<i class='fa fa-picture-o fa-2x' aria-label='placeholder image'></i>";
	}
	$t_list_item = new ca_list_items();
	$t_list_item->load($t_object->get("type_id"));
	$vs_typecode = $t_list_item->get("idno");
	$vs_placeholder = caGetPlaceholder($vs_typecode, "placeholder_media_icon");
	if(!$vs_placeholder){
		$vs_placeholder = $vs_default_placeholder_tag;
	}
	
	$vb_ajax			= (bool)$this->request->isAjax();

	# --- back button for related acquisitions-ca_movements, distributions-ca_loans, archival items - ca_occurrences, ca_entities
	$o_context = new ResultContext($this->request, 'ca_movements', 'detailrelated', 'objects');
	$o_context->setResultList($t_object->get("ca_movements.movement_id", array("returnAsArray" => true, "checkAccess" => $va_access_values)));
	$o_context->setAsLastFind();
	$o_context->saveContext();
	
	$o_context = new ResultContext($this->request, 'ca_loans', 'detailrelated', 'objects');
	$o_context->setResultList($t_object->get("ca_loans.loan_id", array("returnAsArray" => true, "checkAccess" => $va_access_values)));
	$o_context->setAsLastFind();
	$o_context->saveContext();
	
	$o_context = new ResultContext($this->request, 'ca_occurrences', 'detailrelated', 'objects');
	$o_context->setResultList($t_object->get("ca_occurrences.occurrence_id", array("returnAsArray" => true, "checkAccess" => $va_access_values)));
	$o_context->setAsLastFind();
	$o_context->saveContext();
	
	$o_context = new ResultContext($this->request, 'ca_entities', 'detailrelated', 'objects');
	$o_context->setResultList($t_object->get("ca_entities.entity_id", array("returnAsArray" => true, "checkAccess" => $va_access_values)));
	$o_context->setAsLastFind();
	$o_context->saveContext();



	if($vb_ajax){
?>
		<div class="detail detailPreviewContainer">		
			<div class="detailPreview">
				<div class="detailPreviewClose pointer" onclick="caMediaPanel.hidePanel(); return false;"><span class="glyphicon glyphicon-remove-circle"></span></div>
				<div class="row detailPreviewContent">
					<div class="col-sm-10 col-sm-offset-1 height100">			
<?php
						# --- preview panel linked to from image search/browse results
						if($vs_image = $t_object->get("ca_object_representations.media.large")){
							print caDetailLink($this->request, $vs_image, "", "ca_objects", $t_object->get("ca_objects.object_id"));
						}else{
?>
							<?php print caDetailLink($this->request, '<div class="detailPreviewImgPlaceholder">'.$vs_placeholder.'</div>', "", "ca_objects", $t_object->get("ca_objects.object_id")); ?>
<?php
						}
?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-2">
<?php
						if($vs_previous_url = $this->getVar("previousURL")){
							print "<a href='#' onclick='caMediaPanel.showPanel(\"".$vs_previous_url."\"); return false;'><div class='detailPreviewPreviousLink'><i class='fa fa-angle-left'></i><div class='small'>Prev</div></div></a>";
						}
?>
					</div>
					<div class="col-xs-8">
						<div class="unit">
							{{{<label><ifdef code='ca_objects.Object_KressCatalogNumber'>^ca_objects.Object_KressCatalogNumber</ifdef></label><ifdef code="ca_objects.Object_ArtistExpression">^ca_objects.Object_ArtistExpression<br/></ifdef><ifnotdef code="ca_objects.Object_ArtistExpression"><ifcount code="ca_entities" restrictToRelationshipTypes="artist" min="1"><unit relativeTo="ca_entities" restrictToRelationshipTypes="artist"><ifdef code="ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.forename </ifdef><ifdef code="ca_entities.preferred_labels.surname">^ca_entities.preferred_labels.surname</ifdef><ifnotdef code="ca_entities.preferred_labels.surname,ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.displayname</ifnotdef><br/></unit></ifcount></ifnotdef><i>^ca_objects.preferred_labels.name</i>}}}
						</div>
						<p><?php print caDetailLink($this->request, "View Record", "btn btn-default", "ca_objects", $t_object->get("ca_objects.object_id")); ?></p>							
					</div>
					<div class="col-xs-2">
<?php
						if($vs_next_url = $this->getVar("nextURL")){
							print "<a href='#' onclick='caMediaPanel.showPanel(\"".$vs_next_url."\"); return false;'><div class='detailPreviewNextLink'><i class='fa fa-angle-right'></i><div class='small'>Next</div></div></a>";
						}
?>
					</div>
				</div>
			</div><!-- end detailPreview -->				
		</div>		
<?php
	}else{
?>
<div class="row">
	<div class='col-xs-12 navTop'><!--- only shown at small screen size -->
		{{{previousLink}}}{{{resultsLink}}}{{{nextLink}}}
	</div><!-- end detailTop -->
	<div class='navLeftRight col-xs-1 col-sm-1 col-md-1 col-lg-1'>
		<div class="detailNavBgLeft">
			{{{previousLink}}}{{{resultsLink}}}
		</div><!-- end detailNavBgLeft -->
	</div><!-- end col -->
	<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10'>
		<div class="container">
			<div class="row">
				<div class='col-sm-12 col-md-5'>
<?php
					if($vs_rep_viewer){
						print $vs_rep_viewer;
						print '<div id="detailAnnotations"></div>';
						print caObjectRepresentationThumbnails($this->request, $this->getVar("representation_id"), $t_object, array("returnAs" => "bsCols", "linkTo" => "carousel", "bsColClasses" => "smallpadding col-sm-3 col-md-3 col-xs-4", "primaryOnly" => $this->getVar('representationViewerPrimaryOnly') ? 1 : 0));
					}else{
?>
						<div class="detailImgPlaceholder"><?php print $vs_placeholder; ?></div>
<?php
					}
?>
					{{{<ifcount code="ca_objects.related" min="1">
							<br/>
							<ifcount code="ca_objects.related" min="1" max="1">
								<label data-toggle="popover" title="Related Art Object" data-content="Related Art Object">Related Art Object</label>
							</ifcount>
							<ifcount code="ca_objects.related" min="2">
								<label data-toggle="popover" title="Related Art Objects" data-content="Related Art Objects">Related Art Objects</label>
							</ifcount>
							<unit relativeTo="ca_objects.related" delimiter=" ">
									<l><div class="grayBg paddingTop">
										<div class="unit">
											<div class="row">
												<ifdef code="ca_object_representations.media.small">
													<div class="col-xs-4">
														^ca_object_representations.media.small
													</div>
													<div class="col-xs-8">
														<ifdef code='ca_objects.Object_KressCatalogNumber'><small>^ca_objects.Object_KressCatalogNumber</small><br/></ifdef><ifdef code="ca_objects.Object_ArtistExpression">^ca_objects.Object_ArtistExpression<br/></ifdef><ifnotdef code="ca_objects.Object_ArtistExpression"><ifcount code="ca_entities" restrictToRelationshipTypes="artist" min="1"><unit relativeTo="ca_entities" restrictToRelationshipTypes="artist"><ifdef code="ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.forename </ifdef><ifdef code="ca_entities.preferred_labels.surname">^ca_entities.preferred_labels.surname</ifdef><ifnotdef code="ca_entities.preferred_labels.surname,ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.displayname</ifnotdef><br/></unit></ifcount></ifnotdef><i>^ca_objects.preferred_labels.name</i>
													</div>
												</ifdef>
												<ifnotdef code="ca_object_representations.media.small">
													<div class="col-xs-12">
														<ifdef code='ca_objects.Object_KressCatalogNumber'><small>^ca_objects.Object_KressCatalogNumber</small><br/></ifdef><ifdef code="ca_objects.Object_ArtistExpression">^ca_objects.Object_ArtistExpression<br/></ifdef><ifnotdef code="ca_objects.Object_ArtistExpression"><ifcount code="ca_entities" restrictToRelationshipTypes="artist" min="1"><unit relativeTo="ca_entities" restrictToRelationshipTypes="artist"><ifdef code="ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.forename </ifdef><ifdef code="ca_entities.preferred_labels.surname">^ca_entities.preferred_labels.surname</ifdef><ifnotdef code="ca_entities.preferred_labels.surname,ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.displayname</ifnotdef><br/></unit></ifcount></ifnotdef><i>^ca_objects.preferred_labels.name</i>
													</div>
												</ifnotdef>
											</div>
										</div>
									</div></l>
							</unit>
					</ifcount>}}}
				</div><!-- end col -->
				<div class='col-sm-12 col-md-5'>
					<H1>{{{<ifdef code="ca_objects.Object_ArtistExpression">^ca_objects.Object_ArtistExpression<br/></ifdef><ifnotdef code="ca_objects.Object_ArtistExpression"><ifcount code="ca_entities" restrictToRelationshipTypes="artist" min="1"><unit relativeTo="ca_entities" restrictToRelationshipTypes="artist"><ifdef code="ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.forename </ifdef><ifdef code="ca_entities.preferred_labels.surname">^ca_entities.preferred_labels.surname</ifdef><ifnotdef code="ca_entities.preferred_labels.surname,ca_entities.preferred_labels.forename">^ca_entities.preferred_labels.displayname</ifnotdef><br/></unit></ifcount></ifnotdef><i>^ca_objects.preferred_labels.name</i>}}}</H1>
					<div class="grayBg">
						<div class="row">
							{{{<ifdef code="ca_objects.Object_KressCatalogNumber"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Kress Catalog Number" data-content="Kress Catalog Number">Kress Catalog Number</label>^ca_objects.Object_KressCatalogNumber</div></div></ifdef>}}}				
							{{{<ifdef code="ca_objects.idno"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Identifier" data-content="Identifier">Identifier</label>^ca_objects.idno</div></div></ifdef>}}}
						</div>
						<div class="row">
							{{{<ifcount code="ca_entities" restrictToRelationshipTypes="artist" min="1"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Artist" data-content="Artist">Artist</label><unit relativeTo="ca_entities" restrictToRelationshipTypes="artist" delimiter="<br/>"><l>^ca_entities.preferred_labels.displayname</l></unit></div></div></ifcount>}}}
							{{{<ifdef code="ca_objects.Object_Nationality"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Nationality" data-content="Nationality">Nationality</label>^ca_objects.Object_Nationality</div></div></ifdef>}}}
						</div>
						
						<div class="row">
							{{{<ifdef code="ca_objects.Object_DateExpression"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Date" data-content="Date">Date</label>^ca_objects.Object_DateExpression</div></div></ifdef>}}}
							{{{<ifdef code="ca_objects.Object_Medium"><div class="col-sm-6 col-md-6"><div class="unit"><label data-toggle="popover" title="Medium" data-content="Medium">Medium</label>^ca_objects.Object_Medium</div></div></ifdef>}}}
						</div>
						{{{<ifdef code="ca_objects.Object_Classification"><div class="unit"><label data-toggle="popover" title="Classification" data-content="Classification">Classification</label>^ca_objects.Object_Classification</div></ifdef>}}}
						{{{<ifdef code="ca_objects.Object_Dimensions"><div class="unit"><label data-toggle="popover" title="Dimensions" data-content="Dimensions">Dimensions</label>^ca_objects.Object_Dimensions</div></ifdef>}}}
						{{{<ifcount code="ca_entities" restrictToRelationshipTypes="location"><div class="unit"><label data-toggle="popover" title="Location" data-content="Location">Location</label><unit relativeTo="ca_entities" restrictToRelationshipTypes="location" delimiter="<br/>"><l>^ca_entities.preferred_labels.displayname</l></unit></div></ifcount>}}}
					</div>				
				
					{{{<ifcount code="ca_entities" restrictToRelationshipTypes="attribution" min="1"><div class="unit"><label data-toggle="popover" title="Historical Attribution" data-content="Historical Attribution">Historical Attribution</label><unit relativeTo="ca_entities" restrictToRelationshipTypes="attribution" delimiter="<br/>"><l>^ca_entities.preferred_labels.displayname</l></unit></div></ifcount>}}}
					{{{<ifdef code="ca_objects.Object_Provenance">
						<div class='unit'><label data-toggle="popover" title="Provenance" data-content="Provenance">Provenance</label>
							<span class="trimText">^ca_objects.Object_Provenance</span>
						</div>
					</ifdef>}}}
					{{{<ifdef code="ca_objects.Object_Note">
						<div class='unit'><label data-toggle="popover" title="Note" data-content="Note">Note</label>
							<span class="trimText">^ca_objects.Object_Note</span>
						</div>
					</ifdef>}}}
				
					{{{<ifcount code="ca_entities" min="1" excludeRelationshipTypes="artist,location,attribution">
						<div class="unit">
							<label data-toggle="popover" title="Related People & Organizations" data-content="Related People & Organizations">Related People & Organizations</label>
							<unit relativeTo="ca_entities" excludeRelationshipTypes="artist,location,attribution" delimiter="<br/>"><l>^ca_entities.preferred_labels.displayname</l> (^relationship_typename)</unit>
						</div>
					</ifcount>}}}
					{{{<ifdef code="ca_objects.Object_CurrentAccNo"><div class="unit"><label data-toggle="popover" title="Accession Number" data-content="Accession Number">Accession Number</label>^ca_objects.Object_CurrentAccNo</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_KressAssNumber"><div class="unit"><label data-toggle="popover" title="Kress Assigned Number" data-content="Kress Assigned Number">Kress Assigned Number</label>^ca_objects.Object_KressAssNumber</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_AltKressNumber"><div class="unit"><label data-toggle="popover" title="Old or Alternate Kress Number" data-content="Old or Alternate Kress Number">Old or Alternate Kress Number</label>^ca_objects.Object_AltKressNumber</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_PichettoNo"><div class="unit"><label data-toggle="popover" title="Pichetto Number" data-content="Pichetto Number">Pichetto Number</label>^ca_objects.Object_PichettoNo</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_DreyfusNumber"><div class="unit"><label data-toggle="popover" title="Dreyfus Number" data-content="Dreyfus Number">Dreyfus Number</label>^ca_objects.Object_DreyfusNumber</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_NGAOldNumber"><div class="unit"><label data-toggle="popover" title="Old National Gallery of Art Number" data-content="Old National Gallery of Art Number">Old National Gallery of Art Number</label>^ca_objects.Object_NGAOldNumber</div></ifdef>}}}
					{{{<ifdef code="ca_objects.Object_NGAOldLoanNumber"><div class="unit"><label data-toggle="popover" title="Old National Gallery of Art Loan Number" data-content="Old National Gallery of Art Loan Number">Old National Gallery of Art Loan Number</label>^ca_objects.Object_NGAOldLoanNumber</div></ifdef>}}}
					
					
					
						
				</div><!-- end col -->
				<div class='col-sm-12 col-md-2'>
					<div id="detailTools">
	<?php
						print "<div class='detailTool'><span class='glyphicon glyphicon-download' aria-label='"._t("Download Media")."'></span>".caNavLink($this->request, "Download Media", "", "", "Detail",  "DownloadMedia", array('context' => 'objects', 'object_id' => $vn_id, 'version' => 'large', 'download' => 1))."</div>";
						if ($vn_pdf_enabled) {
							print "<div class='detailTool'><span class='glyphicon glyphicon-file' aria-label='"._t("Summary")."'></span>".caDetailLink($this->request, "PDF Summary", "", "ca_objects",  $vn_id, array('view' => 'pdf', 'export_format' => '_pdf_ca_objects_summary'))."</div>";
						}
						print "<div class='detailTool'><span class='glyphicon glyphicon-link' aria-label='"._t("Permalink")."'></span> <a href='#' onClick='$(\"#permalink\").toggle(); return false;'>Permalink</a><br/><textarea name='permalink' id='permalink' class='form-control input-sm' style='display:none;'>".$this->request->config->get("site_host").caDetailUrl($this->request, 'ca_objects', $t_object->get("object_id"))."</textarea></div>";					

	?>
					</div>
				</div>
			</div><!-- end row -->
			<div class="row">
				<div class='col-sm-12'>
					<HR/>
				</div>
			</div>
			<div class="row">
					
<!--				{{{<ifcount code="ca_movements_x_objects" min="1">
					<div class='col-sm-12 col-md-4'>
							<label data-toggle="popover" title="Acquisitions" data-content="Acquisitions">^ca_movements._count Acquisition<ifcount code="ca_movements_x_objects" min="2">s</ifcount></label>
							<unit relativeTo="ca_movements_x_objects" delimiter=" ">
								<l><div class="grayBg paddingTop">
									<div class="unit">
										<unit relativeTo="ca_movements">
											^ca_movements.idno ^ca_movements.preferred_labels
											<ifcount code="ca_entities" min="1"><unit relativeTo="ca_entities"><br>Seller: ^ca_entities.preferred_labels.displayname</unit></ifcount> <br>
										</unit>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_Type">Type: ^ca_movements_x_objects.AcqObjJoin_Type</ifdef>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_Attribution"><br>Attribution History: ^ca_movements_x_objects.AcqObjJoin_Attribution</ifdef>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_PriceUSD"><br>Purchase Amount: ^ca_movements_x_objects.AcqObjJoin_PriceUSD</ifdef>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_CreditUSD"><br>Credit Amount: ^ca_movements_x_objects.AcqObjJoin_CreditUSD</ifdef>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_ReturnUSD"><br>Return Amount: ^ca_movements_x_objects.AcqObjJoin_ReturnUSD</ifdef>
										<ifdef code="ca_movements_x_objects.AcqObjJoin_InternalNote"><br>Note: ^ca_movements_x_objects.AcqObjJoin_InternalNote</ifdef>
									</div>
								</div></l>
							</unit>
					</div>
				</ifcount>}}}
-->
				{{{<ifcount code="ca_movements" min="1">
					<div class='col-sm-12 col-md-4'>
					
							<label data-toggle="popover" title="Acquisitions" data-content="Acquisitions">^ca_movements._count Acquisition<ifcount code="ca_movements" min="2">s</ifcount></label>
							<unit relativeTo="ca_movements" delimiter=" " sort="ca_movements.Acquisition_DateFilter"><l><div class="grayBg paddingTop"><div class="unit">^ca_movements.preferred_labels</div></div></l></unit>
					
					</div>
				</ifcount>}}}
				{{{<ifcount code="ca_loans" min="1">
					<div class='col-sm-12 col-md-4'>
					
							<label data-toggle="popover" title="Distributions" data-content="Distributions">^ca_loans._count Distribution<ifcount code="ca_loans" min="2">s</ifcount></label>
							<unit relativeTo="ca_loans" delimiter=" " sort="ca_loans.Distribution_DateYearFilter"><l><div class="grayBg paddingTop"><div class="unit">^ca_loans.preferred_labels</div></div></l></unit>
					
					</div>
				</ifcount>}}}
			
				{{{<ifdef code="ca_objects.Object_URLCollectionRecord|ca_objects.Object_URLNGALibraryImageURL"><div class='col-sm-12 col-md-4'><label data-toggle="popover" title="External Links" data-content="External Links">External Links</label>					
					<ifdef code="ca_objects.Object_URLCollectionRecord"><a href="^ca_objects.Object_URLCollectionRecord" target="_blank"><div class="grayBg paddingTop"><div class="unit">Related Collection Record <i class="fa fa-external-link" aria-hidden="true"></i></div></div></a></ifdef>
					<ifdef code="ca_objects.Object_URLNGALibraryImageURL"><a href="^ca_objects.Object_URLNGALibraryImageURL" target="_blank"><div class="grayBg paddingTop"><div class="unit">National Gallery of Art Library Image Collection Record <i class="fa fa-external-link" aria-hidden="true"></i></div></div></a></ifdef>
				</div></ifdef>}}}
			
			
			</div>
		{{{<ifcount code="ca_occurrences" min="1" restrictToTypes="documentation">
			<hr/><label data-toggle="popover" title="Archival Materials" data-content="Archival Materials">^ca_occurrences._count Archival Material<ifcount code="ca_occurrences" min="2">s</ifcount></label>						
			<div class="row">
				<unit relativeTo="ca_occurrences" restrictToTypes="documentation" delimiter=" " length="9" sort="ca_occurrences.Doc_DateFilter">
					<div class="col-sm-4">
						<l><div class="grayBg paddingTop">
							<div class="unit">
								<div class="row">
									<div class="col-xs-4">
										^ca_occurrences.media.media_media.iconlarge
									</div>
									<div class="col-xs-8">
										^ca_occurrences.preferred_labels
									</div>
								</div>
							</div>
						</div></l>
					</div>
				</unit>
			</div>			
			<if rule="^ca_occurrences._count > 9" restrictToTypes="documentation">
				<div class="row">
					<div class="col-sm-12 text-center"><?php print caNavLink($this->request, "View All", "btn btn-default", "", "Browse", "archival", array("facet" => "object_facet", "id" => $t_object->get("ca_objects.object_id"))); ?></div>
				</div>
			</if>
		</ifcount>}}}
		</div><!-- end container -->
	</div><!-- end col -->
	<div class='navLeftRight col-xs-1 col-sm-1 col-md-1 col-lg-1'>
		<div class="detailNavBgRight">
			{{{nextLink}}}
		</div><!-- end detailNavBgLeft -->
	</div><!-- end col -->
</div><!-- end row -->

<script type='text/javascript'>
	jQuery(document).ready(function() {
		$('.trimText').readmore({
		  speed: 75,
		  maxHeight: 120
		});
		
		var options = {
			placement: function () {
				if ($(window).width() > 992) {
					return "left";
				}else{
					return "auto top";
				}

			},
			trigger: "hover",
			html: "true"
		};

		$('[data-toggle="popover"]').each(function() {
			if($(this).attr('data-content')){
				$(this).popover(options).click(function(e) {
					$(this).popover('toggle');
				});
			}
		});
	});
</script>
<?php
	}
?>