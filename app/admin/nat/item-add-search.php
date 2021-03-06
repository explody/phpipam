<?php

/**
 *	remove item from nat
 ************************************************/

# validate csrf cookie
$Tools->csrf_validate($csrf, $Result);

# length
if(strlen($_POST['ip'])==0)   { $Result->show("danger", _("Please enter IP address"), true); }
# id
if(!is_numeric($_POST['id'])) { $Result->show("danger", _("Invalid NAT item ID"), true); }
# type
if($_POST['type']!=="src" && $_POST['type']!=="dst" ) { $Result->show("danger", _("Invalid NAT direction"), true); }

# set searchterm
if(isset($_POST['ip'])) {
	// trim
	$_POST['ip'] = trim($_POST['ip']);
	// escape
	$_POST['ip'] = htmlspecialchars($_POST['ip']);

	$search_term = @$search_term=="search" ? "" : $_POST['ip'];
}

# change * to % for database wildchar
$search_term = trim($search_term);
$search_term = str_replace("*", "%", $search_term);

# fetch old details
$nat = $Tools->fetch_object("nat", "id", $_POST['id']);
$nat->src = json_decode($nat->src, true);
$nat->dst = json_decode($nat->dst, true);

// identify
$type = $Admin->identify_address( $search_term ); //identify address type

# reformat if IP address for search
if ($type == "IPv4") 		{ $search_term_edited = $Tools->reformat_IPv4_for_search ($search_term); }	//reformat the IPv4 address!
elseif($type == "IPv6") 	{ $search_term_edited = $Tools->reformat_IPv6_for_search ($search_term); }	//reformat the IPv4 address!

# search addresses
$result_addresses = $Tools->search_addresses($search_term, $search_term_edited['high'], $search_term_edited['low'], array());
# search subnets
$result_subnets   = $Tools->search_subnets($search_term, $search_term_edited['high'], $search_term_edited['low'], $_POST['ip']. array());

# if some found print
if(sizeof($result_addresses)>0 && sizeof($result_subnets)>0) {
    if(sizeof($result_subnets)>0) {
        $html1[] = "<h4>Subnets</h4>";
        foreach ($result_subnets as $s) {
            if(!@in_array($s->id, $nat->src['subnets']) && !@in_array($s->id, $nat->dst['subnets']))
            $html1[] = "<a class='btn btn-xs btn-success addNatObjectFromSearch' data-id='".$_POST['id']."' data-object-id='$s->id' data-object-type='subnets' data-type='".$_POST['type']."'><i class='fa fa-plus'></i></a> ".$Tools->transform_address($s->subnet, "dotted")."/".$s->mask."<br>";
        }
        if(sizeof($html1)==1) { $html1 = array(); }
    }
    if(sizeof($result_addresses)>0) {
        $html2[] = "<h4>Addresses</h4>";
        foreach ($result_addresses as $a) {
            if(!@in_array($a->id, $nat->src['ipaddresses']) && !@in_array($a->id, $nat->dst['ipaddresses']))
            $html2[] = "<a class='btn btn-xs btn-success addNatObjectFromSearch' data-id='".$_POST['id']."' data-object-id='$a->id' data-object-type='ipaddresses' data-type='".$_POST['type']."'><i class='fa fa-plus'></i></a> ".$Tools->transform_address($a->ip_addr, "dotted")."<br>";
        }
        if(sizeof($html2)==1) { $html2 = array(); }
    }
    // print
    print implode("\n", $html1);
    print implode("\n", $html2);
}
else {
    $Result->show("info", _("No results found"), false);
}
?>
