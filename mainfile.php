<?php
/**
 * Plugin Name: CYA Yachts (Opus)
 * Description: Show yachts from Central Yacht Agent API on opusyachtcharters.com (XML list + JSON e-brochure detail)
 * Version: 1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 🔹 Value helper
 */
function opus_cya_has_value( $value ) {
    $value = trim( (string) $value );

    if ( $value === '' ) {
        return false;
    }
    if ( $value === '0' ) {
        return false;
    }
    if ( $value === '-' ) {
        return false;
    }

    return true;
}

function opus_cya_li( $label, $value ) {
    if ( ! opus_cya_has_value( $value ) ) {
        return '';
    }

    return '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '</li>';
}

/**
 * 🔹 CYA Location codes (from locationcodes.php)
 */
function opus_cya_get_locations() {
    return array(
        'src50' => 'Adriatic Sea',
        'src13' => 'Alaska',
        'src28' => 'Antarctica',
        'src29' => 'Arctic',
        'src21' => 'Australia',
        'src5'  => 'Bahamas',
        'src35' => 'Belize',
        'src42' => 'Bermuda',
        'src38' => 'Canada - East Coast',
        'src39' => 'Canada - West Coast',
        'src34' => 'Canary Islands',
        'src48' => 'Caribbean All',
        'src7'  => 'Caribbean Leewards',
        'src46' => 'Caribbean Virgin Islands (BVI)',
        'src45' => 'Caribbean Virgin Islands (US)',
        'src3'  => 'Caribbean Virgin Islands (US/BVI)',
        'src8'  => 'Caribbean Windwards',
        'src32' => 'Cayman Islands',
        'src20' => 'Central America',
        'src51' => 'Costa Rica',
        'src16' => 'Croatia',
        'src52' => 'Croatia - Skradin',
        'src40' => 'Dominican Republic',
        'src26' => 'Dubai',
        'src30' => 'French Polynesia',
        'src31' => 'Galapagos',
        'src4'  => 'Greece',
        'src12' => 'Indian Ocean and SE Asia',
        'src53' => 'Malta',
        'src19' => 'Mexico',
        'src49' => 'Montenegro',
        'src22' => 'New Zealand',
        'src24' => 'Northern Europe',
        'src14' => 'Pacific NW',
        'src25' => 'Red Sea',
        'src2'  => 'South America',
        'src33' => 'South China Sea',
        'src37' => 'South East Asia',
        'src23' => 'South Pacific',
        'src11' => 'Turkey',
        'src41' => 'Turks and Caicos',
        'src27' => 'United Arab Emirates',
        'src36' => 'USA - Annapolis - MD',
        'src17' => 'USA - California',
        'src10' => 'USA - Florida East Coast',
        'src47' => 'USA - Florida West Coast',
        'src18' => 'USA - Great Lakes',
        'src9'  => 'USA - New England',
        'src44' => 'USA - North East',
        'src43' => 'USA - South East',
        'src15' => 'W. Med - Spain/Balearics',
        'src1'  => 'W. Med - Naples/Sicily',
        'src6'  => 'W. Med - Riviera/Cors/Sard.'
    );
}

/**
 * 🔧 Shared filter HTML builder
 *   – [cya_yachts] + [cya_yachts_filter]
 */
function opus_cya_yachts_build_filter_html(
    $name_options,
    $selected_name,
    $selected_boattype,
    $selected_ylocations,
    $selected_guests,
    $selected_pricefrom,
    $selected_priceto,
    $selected_sortby
) {
    // Clean action URL for reset
    $action_url = esc_url( remove_query_arg(
        array( 'cy_name', 'cy_boattype', 'cy_ylocations', 'cy_guests', 'cy_pricefrom', 'cy_priceto', 'cy_sortby', 'cy_page' )
    ) );

    $locations = opus_cya_get_locations();

    // FILTER FORM – dark + gold theme
    $html  = '<form method="get" action="' . $action_url . '" class="cya-yacht-filter" style="margin:24px auto 24px auto;padding:18px 18px 20px;border-radius:18px;border:1px solid rgba(211,175,55,0.6);background:radial-gradient(circle at top,#00291A,#000000);color:#f5f5f5;width:80%;max-width:1600px;box-shadow:0 18px 40px rgba(0,0,0,0.85);">';
    $html .= '  <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">';

    // Common field shell inline style
    $field_shell = 'flex:1 1 200px;min-width:200px;';
    $compact_shell = 'flex:0 0 140px;min-width:120px;';
    $mini_shell = 'flex:0 0 120px;min-width:100px;';

    $label_style = 'display:block;font-size:11px;font-weight:600;margin-bottom:4px;letter-spacing:0.06em;text-transform:uppercase;color:#D3AF37;';

    $select_style = 'width:100%;padding:8px 10px;font-size:14px;border-radius:999px;border:1px solid rgba(211,175,55,0.5);background:#000000;color:#f5f5f5;outline:none;box-shadow:none;';

    $input_style  = 'width:100%;padding:8px 10px;font-size:14px;border-radius:999px;border:1px solid rgba(211,175,55,0.5);background:#000000;color:#f5f5f5;outline:none;box-shadow:none;';

    // Yacht Name
    $html .= '    <div style="' . $field_shell . '">';
    $html .= '      <label style="' . $label_style . '">Yacht Name</label>';
    $html .= '      <select name="cy_name" style="' . $select_style . '">';
    $html .= '        <option value="">All Yachts</option>';

    if ( ! empty( $name_options ) && is_array( $name_options ) ) {
        foreach ( $name_options as $name ) {
            $html .= '<option value="' . esc_attr( $name ) . '"' . selected( $selected_name, $name, false ) . '>' . esc_html( $name ) . '</option>';
        }
    }

    $html .= '      </select>';
    $html .= '    </div>';

    // Boat Type
    $html .= '    <div style="' . $field_shell . '">';
    $html .= '      <label style="' . $label_style . '">Boat Type</label>';
    $html .= '      <select name="cy_boattype" style="' . $select_style . '">';
    $html .= '        <option value="">All Types</option>';
    $html .= '        <option value="P"'  . selected( $selected_boattype, 'P', false )  . '>Power</option>';
    $html .= '        <option value="C"'  . selected( $selected_boattype, 'C', false )  . '>Catamarans</option>';
    $html .= '        <option value="S"'  . selected( $selected_boattype, 'S', false )  . '>Mono-Hulls</option>';
    $html .= '        <option value="SC"' . selected( $selected_boattype, 'SC', false ) . '>Mono-Hulls & Catamarans</option>';
    $html .= '        <option value="PC"' . selected( $selected_boattype, 'PC', false ) . '>Power Catamarans (No Sails)</option>';
    $html .= '        <option value="M"'  . selected( $selected_boattype, 'M', false )  . '>Motor Sailers</option>';
    $html .= '      </select>';
    $html .= '    </div>';

    // Location
    $html .= '    <div style="' . $field_shell . '">';
    $html .= '      <label style="' . $label_style . '">Location</label>';
    $html .= '      <select name="cy_ylocations" style="' . $select_style . '">';
    $html .= '        <option value="">All Locations</option>';
    foreach ( $locations as $code => $label ) {
        $html .= '        <option value="' . esc_attr( $code ) . '"' . selected( $selected_ylocations, $code, false ) . '>' . esc_html( $label ) . '</option>';
    }
    $html .= '      </select>';
    $html .= '    </div>';

    // Guests
    $html .= '    <div style="' . $mini_shell . '">';
    $html .= '      <label style="' . $label_style . '">Guests</label>';
    $html .= '      <input type="number" min="1" name="cy_guests" value="' . esc_attr( $selected_guests ) . '" style="' . $input_style . '">';
    $html .= '    </div>';

    // Price From
    $html .= '    <div style="' . $compact_shell . '">';
    $html .= '      <label style="' . $label_style . '">Price From</label>';
    $html .= '      <input type="number" min="0" step="1000" name="cy_pricefrom" value="' . esc_attr( $selected_pricefrom ) . '" style="' . $input_style . '">';
    $html .= '    </div>';

    // Price To
    $html .= '    <div style="' . $compact_shell . '">';
    $html .= '      <label style="' . $label_style . '">Price To</label>';
    $html .= '      <input type="number" min="0" step="1000" name="cy_priceto" value="' . esc_attr( $selected_priceto ) . '" style="' . $input_style . '">';
    $html .= '    </div>';

    // Sort By
    $html .= '    <div style="flex:0 0 200px;min-width:170px;">';
    $html .= '      <label style="' . $label_style . '">Sort By</label>';
    $html .= '      <select name="cy_sortby" style="' . $select_style . '">';
    $html .= '        <option value="1"' . selected( $selected_sortby, '1', false ) . '>Length (Descending)</option>';
    $html .= '        <option value="2"' . selected( $selected_sortby, '2', false ) . '>Length (Ascending)</option>';
    $html .= '        <option value="3"' . selected( $selected_sortby, '3', false ) . '>Name A-Z</option>';
    $html .= '        <option value="4"' . selected( $selected_sortby, '4', false ) . '>Name Z-A</option>';
    $html .= '        <option value="5"' . selected( $selected_sortby, '5', false ) . '>Guests (Ascending)</option>';
    $html .= '        <option value="6"' . selected( $selected_sortby, '6', false ) . '>Guests (Descending)</option>';
    $html .= '        <option value="7"' . selected( $selected_sortby, '7', false ) . '>Price (Ascending)</option>';
    $html .= '        <option value="8"' . selected( $selected_sortby, '8', false ) . '>Price (Descending)</option>';
    $html .= '      </select>';
    $html .= '    </div>';

    // Reset
    $html .= '    <div style="flex:0 0 140px;align-self:flex-end;display:flex;gap:8px;min-width:120px;justify-content:flex-end;">';
    $html .= '      <button type="submit" style="display:none;">Apply</button>';

    $reset_style = 'flex:1;padding:8px 16px;font-size:12px;border-radius:999px;'
                 . 'border:1px solid rgba(211,175,55,0.9);'
                 . 'background:#000000;color:#f5f5f5;text-align:center;'
                 . 'text-decoration:none;line-height:1.2;letter-spacing:0.08em;text-transform:uppercase;'
                 . 'transition:all .2s ease;font-weight:600;';

    $html .= '      <a href="' . $action_url . '" '
           . 'style="' . $reset_style . '" '
           . 'onmouseover="this.style.backgroundColor=\'#D3AF37\';this.style.color=\'#000000\';this.style.boxShadow=\'0 0 18px rgba(211,175,55,0.7)\';" '
           . 'onmouseout="this.style.backgroundColor=\'#000000\';this.style.color=\'#f5f5f5\';this.style.boxShadow=\'none\';">'
           . 'Reset'
           . '</a>';
    $html .= '    </div>';

    $html .= '  </div>';
    $html .= '</form>';

    // Fullscreen loader for filters
    static $script_added = false;
    if ( ! $script_added ) {
        $script_added = true;
        $html .= '<script>
document.addEventListener("DOMContentLoaded", function() {

  (function() {
    if (document.getElementById("cya-loader-style")) return;
    var style = document.createElement("style");
    style.id = "cya-loader-style";
    style.innerHTML = `
      @keyframes cya-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      .cya-spinner {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.12);
        border-top-color: #D3AF37;
        animation: cya-spin 0.8s linear infinite;
        margin-right: 10px;
      }
    `;
    document.head.appendChild(style);
  })();

  function ensureCyaLoader() {
    var existing = document.getElementById("cya-fullscreen-loader");
    if (existing) return existing;

    var loader = document.createElement("div");
    loader.id = "cya-fullscreen-loader";
    loader.style.position = "fixed";
    loader.style.inset = "0";
    loader.style.zIndex = "9999";
    loader.style.background = "rgba(0,0,0,0.55)";
    loader.style.display = "none";
    loader.style.alignItems = "center";
    loader.style.justifyContent = "center";
    loader.style.backdropFilter = "blur(2px)";
    loader.innerHTML = \'<div style="background:#000000;padding:16px 24px;border-radius:12px;font-size:14px;font-weight:600;box-shadow:0 10px 40px rgba(0,0,0,0.7);display:flex;align-items:center;border:1px solid rgba(211,175,55,0.9);color:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,system-ui,sans-serif;">\' +
                       \'<div class="cya-spinner"></div>\' +
                       \'<span>Loading yachts...</span>\' +
                       \'</div>\';
    document.body.appendChild(loader);
    return loader;
  }

  function showCyaLoader() {
    var l = ensureCyaLoader();
    l.style.display = "flex";
  }

  var forms = document.querySelectorAll(".cya-yacht-filter");
  forms.forEach(function(form) {
    var autoFields = form.querySelectorAll(
      "select, input[name=\'cy_guests\'], input[name=\'cy_pricefrom\'], input[name=\'cy_priceto\']"
    );

    autoFields.forEach(function(field) {
      field.addEventListener("change", function() {
        showCyaLoader();
        form.submit();
      });
    });

    form.addEventListener("keyup", function(e) {
      if (
        (e.target.name === "cy_pricefrom" ||
         e.target.name === "cy_priceto"  ||
         e.target.name === "cy_guests") &&
        e.key === "Enter"
      ) {
        showCyaLoader();
        form.submit();
      }
    });

    form.addEventListener("submit", function() {
      showCyaLoader();
    });
  });
});
</script>

<style>
/* Pagination alignment */
.cya-pagination{
    justify-content:center !important;
}

/* Fallback pagination links (inline styles already set from PHP, yeh sirf backup hai) */
nav.cya-pagination a {
    font-size:13px;
    font-weight:500;
    color:#f5f5f5;
}

/* Fallback button style – cards ke liye (PHP inline styles dominate) */
.cya-yacht-content a {
    border-radius:999px;
    text-decoration:none !important;
}

/* Meta cards typography backup */
.box1 h3 {
    letter-spacing:0.05em;
}
.box1 ul li {
    line-height:1.7;
}

/* Global font fallback */
#cya-yacht-ebrochure1{
     font-family:"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif !important;
}

/* Filter elements focus state */
.cya-yacht-filter select:focus,
.cya-yacht-filter input:focus {
    box-shadow:0 0 0 1px rgba(211,175,55,0.9);
    border-color:rgba(211,175,55,0.9) !important;
}
</style>
';
    }

    return $html;
}


/**
 * SHORTCODE 1: List + filter + pagination
 * [cya_yachts]
 */
add_shortcode( 'cya_yachts', 'opus_cya_yachts_shortcode' );

function opus_cya_yachts_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'boattype'   => '',
        'ylocations' => '',
        'guests'     => '',
        'pricefrom'  => '',
        'priceto'    => '',
        'sortby'     => '1',
    ), $atts, 'cya_yachts' );

    // Filters from URL
    $selected_name       = isset( $_GET['cy_name'] )       ? sanitize_text_field( wp_unslash( $_GET['cy_name'] ) )       : '';
    $selected_boattype   = isset( $_GET['cy_boattype'] )   ? sanitize_text_field( wp_unslash( $_GET['cy_boattype'] ) )   : $atts['boattype'];
    $selected_ylocations = isset( $_GET['cy_ylocations'] ) ? sanitize_text_field( wp_unslash( $_GET['cy_ylocations'] ) ) : $atts['ylocations'];
    $selected_guests     = isset( $_GET['cy_guests'] )     ? sanitize_text_field( wp_unslash( $_GET['cy_guests'] ) )     : $atts['guests'];
    $selected_pricefrom  = isset( $_GET['cy_pricefrom'] )  ? sanitize_text_field( wp_unslash( $_GET['cy_pricefrom'] ) )  : $atts['pricefrom'];
    $selected_priceto    = isset( $_GET['cy_priceto'] )    ? sanitize_text_field( wp_unslash( $_GET['cy_priceto'] ) )    : $atts['priceto'];
    $selected_sortby     = isset( $_GET['cy_sortby'] )     ? sanitize_text_field( wp_unslash( $_GET['cy_sortby'] ) )     : $atts['sortby'];

    // Pagination params
    $current_page = isset( $_GET['cy_page'] ) ? max( 1, intval( $_GET['cy_page'] ) ) : 1;
    $per_page     = 12;

    // API values
    $api_boattype   = $selected_boattype;
    $api_ylocations = $selected_ylocations;

    // CYA rule: at least 1 of boattype/ylocations/yachtname
    if ( $api_boattype === '' && $api_ylocations === '' && $selected_name === '' ) {
        $api_boattype = 'SC'; // default
    }

    $api_url = 'https://www.centralyachtagent.com/snapins/snyachts-xml.php';

    $body = array(
        'user'       => '3772',
        'boattype'   => $api_boattype,
        'ylocations' => $api_ylocations,
        'guests'     => $selected_guests,
        'pricefrom'  => $selected_pricefrom,
        'priceto'    => $selected_priceto,
        'sortby'     => $selected_sortby,
        'yachtname'  => $selected_name,
    );

    $response = wp_remote_post( $api_url, array(
        'body'    => $body,
        'timeout' => 20,
    ) );

    if ( is_wp_error( $response ) ) {
        return '<p>API Error: ' . esc_html( $response->get_error_message() ) . '</p>';
    }

    $xml_string = wp_remote_retrieve_body( $response );

    if ( empty( $xml_string ) ) {
        $out  = opus_cya_yachts_build_filter_html(
            array(),
            $selected_name,
            $selected_boattype,
            $selected_ylocations,
            $selected_guests,
            $selected_pricefrom,
            $selected_priceto,
            $selected_sortby
        );
        $out .= '<p>No data returned from CYA API.</p>';
        return $out;
    }

    $xml = simplexml_load_string( $xml_string );

    if ( ! $xml || ! isset( $xml->yacht ) ) {
        $out  = opus_cya_yachts_build_filter_html(
            array(),
            $selected_name,
            $selected_boattype,
            $selected_ylocations,
            $selected_guests,
            $selected_pricefrom,
            $selected_priceto,
            $selected_sortby
        );
        $out .= '<p>No yachts found for this search.</p>';
        return $out;
    }

    // Sab yachts array me lao
    $yacht_array = array();
    foreach ( $xml->yacht as $yacht ) {
        $yacht_array[] = $yacht;
    }

    $total_results = count( $yacht_array );
    $total_pages   = max( 1, (int) ceil( $total_results / $per_page ) );

    if ( $current_page > $total_pages ) {
        $current_page = $total_pages;
    }

    $offset       = ( $current_page - 1 ) * $per_page;
    $paged_yachts = array_slice( $yacht_array, $offset, $per_page );

    // Yacht names for dropdown (from full list)
    $name_options = array();
    foreach ( $xml->yacht as $yacht ) {
        $n = trim( (string) $yacht->yachtName );
        if ( $n !== '' && ! in_array( $n, $name_options, true ) ) {
            $name_options[] = $n;
        }
    }
    sort( $name_options, SORT_NATURAL | SORT_FLAG_CASE );

    // Filter form (isko abhi as-is rehne diya, agar chaho to baad me iski styling bhi yehi colors pe kar denge)
    $html  = opus_cya_yachts_build_filter_html(
        $name_options,
        $selected_name,
        $selected_boattype,
        $selected_ylocations,
        $selected_guests,
        $selected_pricefrom,
        $selected_priceto,
        $selected_sortby
    );

    // Wrapper for AJAX replace + dark background
    $html .= '<div id="cya-yachts-wrapper" style="background:#000000;padding:32px 0 40px 0;border-top:1px solid rgba(211,175,55,0.35);">';

    // Grid container
    $html .= '<div id="cya-yacht-ebrochure1" class="cya-yachts-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;width:80%;max-width:1600px;margin:0 auto;">';

    $found = 0;

    // Common card style
    $card_style = 'border-radius:16px;overflow:hidden;'
        . 'background:radial-gradient(circle at top,#00291A,#000000);'
        . 'border:1px solid rgba(211,175,55,0.7);'
        . 'box-shadow:0 14px 38px rgba(0,0,0,0.85);'
        . 'color:#f5f5f5;';

    foreach ( $paged_yachts as $yacht ) {
        $id    = (string) $yacht->yachtId;
        $name  = (string) $yacht->yachtName;

        // Extra safety: name filter
        if ( $selected_name !== '' && $name !== $selected_name ) {
            continue;
        }

        $size      = (string) $yacht->sizeFeet;
        $pax       = (string) $yacht->yachtPax;
        $cabins    = (string) $yacht->yachtCabins;
        $lowPrice  = (string) $yacht->yachtLowPrice;
        $highPrice = (string) $yacht->yachtHighPrice;
        $thumb     = (string) $yacht->yachtEbrochureThumb;

        if ( empty( $thumb ) ) {
            $thumb = 'https://via.placeholder.com/600x400?text=Yacht';
        }

        $found++;

        $html .= '<div id="cya-yacht-ebrochure1" class="cya-yacht-card" style="' . $card_style . '">';

        // Image
        $html .= '  <div class="cya-yacht-image" style="position:relative;overflow:hidden;max-height:230px;">';
        $html .= '    <img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $name ) . '" style="width:100%;height:230px;object-fit:cover;display:block;filter:brightness(0.9);transition:transform .35s ease,filter .35s ease;" onmouseover="this.style.transform=\'scale(1.05)\';this.style.filter=\'brightness(1)\';" onmouseout="this.style.transform=\'scale(1)\';this.style.filter=\'brightness(0.9)\';">';
        $html .= '    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.6),transparent);pointer-events:none;"></div>';
        $html .= '  </div>';

        // Content
        $html .= '  <div class="cya-yacht-content" style="padding:16px 16px 18px 16px;">';
        $html .= '    <h3 style="margin:0 0 6px;font-size:18px;font-weight:600;color:#D3AF37;letter-spacing:0.04em;text-transform:uppercase;">' . esc_html( $name ) . '</h3>';
        $html .= '    <p style="margin:0 0 4px;font-size:14px;color:#f2f2f2;opacity:0.9;">' 
               . esc_html( $size ) . ' • ' . esc_html( $cabins ) . ' cabins • ' . esc_html( $pax ) . ' guests</p>';

        if ( $lowPrice || $highPrice ) {
            $price_text = '';
            if ( $lowPrice && $highPrice && $lowPrice !== $highPrice ) {
                $price_text = $lowPrice . ' - ' . $highPrice;
            } else {
                $price_text = ( $lowPrice ?: $highPrice );
            }
            $html .= '    <p style="margin:6px 0 0;font-weight:600;font-size:14px;color:#D3AF37;">' . esc_html( $price_text ) . '</p>';
        }

        $detail_url = add_query_arg(
            array(
                'yid' => $id,
            ),
            site_url( '/yacht-detail/' )
        );

        // View button – pill, gold border
        $btn_style = 'display:inline-block;margin-top:10px;font-size:13px;'
                   . 'padding:7px 16px;border-radius:999px;'
                   . 'border:1px solid rgba(211,175,55,0.9);'
                   . 'background:rgba(0,0,0,0.9);color:#f5f5f5;'
                   . 'text-decoration:none;letter-spacing:0.05em;text-transform:uppercase;'
                   . 'transition:all .2s ease;';

        $btn_hover = "this.style.backgroundColor='rgba(211,175,55,0.95)';"
                   . "this.style.color='#000000';"
                   . "this.style.boxShadow='0 0 18px rgba(211,175,55,0.7)';";

        $btn_out   = "this.style.backgroundColor='rgba(0,0,0,0.9)';"
                   . "this.style.color='#f5f5f5';"
                   . "this.style.boxShadow='none';";

        $html .= '    <a href="' . esc_url( $detail_url ) . '" class="btn-style" '
               . 'style="' . $btn_style . '" '
               . 'onmouseover="' . $btn_hover . '" '
               . 'onmouseout="' . $btn_out . '">'
               . 'View full brochure'
               . '</a>';

        $html .= '  </div>'; // content
        $html .= '</div>';    // card
    }

    // No yachts after filter
    if ( $found === 0 ) {
        $active_filters = array();

        if ( $selected_name !== '' )       $active_filters[] = 'yacht name';
        if ( $selected_boattype !== '' )   $active_filters[] = 'boat type';
        if ( $selected_ylocations !== '' ) $active_filters[] = 'location';
        if ( $selected_guests !== '' )     $active_filters[] = 'guest count';
        if ( $selected_pricefrom !== '' || $selected_priceto !== '' ) {
            $active_filters[] = 'price range';
        }

        $msg = 'No yachts found';

        if ( ! empty( $active_filters ) ) {
            $last = array_pop( $active_filters );
            if ( ! empty( $active_filters ) ) {
                $msg .= ' for your selected ' . implode( ', ', $active_filters ) . ' and ' . $last . '.';
            } else {
                $msg .= ' for your selected ' . $last . '.';
            }
            $msg .= ' Please adjust your filters or click Reset to see more yachts.';
        } else {
            $msg .= ' for this search. Please try changing your filters and search again.';
        }

        $html .= '<p class="cya-yachts-empty" style="grid-column:1/-1;padding:20px 18px;border-radius:14px;'
              . 'background:rgba(0,41,26,0.9);border:1px solid rgba(211,175,55,0.8);'
              . 'color:#f5f5f5;font-size:14px;">'
              . esc_html( $msg )
              . '</p>';
    }

    $html .= '</div>'; // .cya-yachts-grid

    // Pagination nav
    if ( $total_pages > 1 ) {
        $html .= '<nav id="cya-yacht-ebrochure1" class="cya-pagination" '
               . 'style="margin-top:24px;display:flex;flex-wrap:wrap;gap:6px;'
               . 'align-items:center;justify-content:center;width:80%;max-width:1600px;margin-left:auto;margin-right:auto;">';

        // Base URL (current) without cy_page
        $base_url = remove_query_arg( 'cy_page' );

        // Common args (filters)
        $common_args = array(
            'cy_name'       => $selected_name ?: null,
            'cy_boattype'   => $selected_boattype ?: null,
            'cy_ylocations' => $selected_ylocations ?: null,
            'cy_guests'     => $selected_guests ?: null,
            'cy_pricefrom'  => $selected_pricefrom ?: null,
            'cy_priceto'    => $selected_priceto ?: null,
            'cy_sortby'     => $selected_sortby ?: null,
        );

        // Small helper: page link HTML
        $make_page_link = function( $page, $label = null ) use ( $base_url, $common_args, $current_page ) {
            $label = $label ?? $page;

            $page_args = array_merge( $common_args, array(
                'cy_page' => $page,
            ) );
            $page_url = esc_url( add_query_arg( $page_args, $base_url ) );

            // Active page
            if ( $page === $current_page ) {
                return '<span style="padding:7px 12px;border-radius:999px;'
                     . 'border:1px solid rgba(211,175,55,0.9);'
                     . 'background:#D3AF37;color:#000000;font-size:13px;font-weight:600;">'
                     . esc_html( $label )
                     . '</span>';
            }

            // Inactive link
            return '<a href="' . $page_url . '" class="cya-page-link" data-page="' . $page . '" '
                 . 'style="padding:7px 12px;border-radius:999px;'
                 . 'border:1px solid rgba(211,175,55,0.6);'
                 . 'background:#000000;color:#f5f5f5;font-size:13px;text-decoration:none;'
                 . 'transition:all .2s ease;" '
                 . 'onmouseover="this.style.backgroundColor=\'#D3AF37\';this.style.color=\'#000000\';" '
                 . 'onmouseout="this.style.backgroundColor=\'#000000\';this.style.color=\'#f5f5f5\';">'
                 . esc_html( $label )
                 . '</a>';
        };

        // Prev
        if ( $current_page > 1 ) {
            $html .= $make_page_link( $current_page - 1, '« Prev' );
        }

        // Agar pages kam hain (<=7) to sab dikha do
        if ( $total_pages <= 7 ) {

            for ( $i = 1; $i <= $total_pages; $i++ ) {
                $html .= $make_page_link( $i );
            }

        } else {
            // Zyada pages – compact style:
            // 1 2 3 ... 10
            // 1 ... 4 5 6 ... 10
            // 1 ... 8 9 10

            // Always first page
            $html .= $make_page_link( 1 );

            // Window around current (excluding 1 and last)
            $start = max( 2, $current_page - 1 );
            $end   = min( $total_pages - 1, $current_page + 1 );

            // Agar start > 2 to matlab beech me gap hai -> "..."
            if ( $start > 2 ) {
                $html .= '<span style="padding:6px 6px;font-size:13px;color:#f5f5f5;">...</span>';
            }

            // Middle pages
            for ( $i = $start; $i <= $end; $i++ ) {
                $html .= $make_page_link( $i );
            }

            // Agar end < last-1 to yahan bhi gap -> "..."
            if ( $end < $total_pages - 1 ) {
                $html .= '<span style="padding:6px 6px;font-size:13px;color:#f5f5f5;">...</span>';
            }

            // Always last page
            $html .= $make_page_link( $total_pages );
        }

        // Next
        if ( $current_page < $total_pages ) {
            $html .= $make_page_link( $current_page + 1, 'Next »' );
        }

        $html .= '</nav>';
    }

    $html .= '</div>'; // #cya-yachts-wrapper

    return $html;
}

/**
 * SHORTCODE 1b: filter only
 * [cya_yachts_filter]
 */
add_shortcode( 'cya_yachts_filter', 'opus_cya_yachts_filter_only_shortcode' );

function opus_cya_yachts_filter_only_shortcode( $atts ) {
    $defaults = array(
        'name'       => '',
        'boattype'   => '',
        'ylocations' => '',
        'guests'     => '',
        'pricefrom'  => '',
        'priceto'    => '',
        'sortby'     => '1',
    );
    $atts = shortcode_atts( $defaults, $atts, 'cya_yachts_filter' );

    $selected_name       = isset( $_GET['cy_name'] )       ? sanitize_text_field( wp_unslash( $_GET['cy_name'] ) )       : $atts['name'];
    $selected_boattype   = isset( $_GET['cy_boattype'] )   ? sanitize_text_field( wp_unslash( $_GET['cy_boattype'] ) )   : $atts['boattype'];
    $selected_ylocations = isset( $_GET['cy_ylocations'] ) ? sanitize_text_field( wp_unslash( $_GET['cy_ylocations'] ) ) : $atts['ylocations'];
    $selected_guests     = isset( $_GET['cy_guests'] )     ? sanitize_text_field( wp_unslash( $_GET['cy_guests'] ) )     : $atts['guests'];
    $selected_pricefrom  = isset( $_GET['cy_pricefrom'] )  ? sanitize_text_field( wp_unslash( $_GET['cy_pricefrom'] ) )  : $atts['pricefrom'];
    $selected_priceto    = isset( $_GET['cy_priceto'] )    ? sanitize_text_field( wp_unslash( $_GET['cy_priceto'] ) )    : $atts['priceto'];
    $selected_sortby     = isset( $_GET['cy_sortby'] )     ? sanitize_text_field( wp_unslash( $_GET['cy_sortby'] ) )     : $atts['sortby'];

    return opus_cya_yachts_build_filter_html(
        array(),
        $selected_name,
        $selected_boattype,
        $selected_ylocations,
        $selected_guests,
        $selected_pricefrom,
        $selected_priceto,
        $selected_sortby
    );
}

/**
 * SHORTCODE 2: Single yacht JSON e-brochure
 * [cya_yacht id="10216"]
 */
add_shortcode( 'cya_yacht', 'opus_cya_yacht_ebrochure_shortcode' );

function opus_cya_yacht_ebrochure_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id'    => '',
        'debug' => '',
    ), $atts, 'cya_yacht_ebrochure' );

    if ( empty( $atts['id'] ) ) {
        return '<p>No yacht ID provided.</p>';
    }

    $api_url = 'https://www.centralyachtagent.com/snapins/json-ebrochure.php';

    $params = array(
        'idin'    => $atts['id'],
        'user'    => '3772',
        'apicode' => '3772-OYC5618Thgs5gesu45nhgTs',
    );

    $request_url = add_query_arg( $params, $api_url );

    $response = wp_remote_get( $request_url, array(
        'timeout' => 20,
    ) );

    if ( is_wp_error( $response ) ) {
        return '<p>API Error: ' . esc_html( $response->get_error_message() ) . '</p>';
    }

    $json_string = wp_remote_retrieve_body( $response );

    if ( empty( $json_string ) ) {
        return '<p>No data returned from CYA JSON e-brochure API.</p>';
    }

    if ( ! empty( $atts['debug'] ) ) {
        $decoded = json_decode( $json_string, true );

        $html_debug  = '<pre style="white-space:pre-wrap;font-size:12px;">'
            . esc_html( $request_url . "\n\n" . $json_string )
            . '</pre>';

        $html_debug .= '<script>console.log("CYA JSON ebrochure:", ' 
            . wp_json_encode( $decoded ) 
            . ');</script>';

        return $html_debug;
    }

    $data = json_decode( $json_string, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return '<p>Invalid JSON from CYA API: ' . esc_html( json_last_error_msg() ) . '</p>';
    }

    if ( empty( $data['yacht'] ) || ! is_array( $data['yacht'] ) ) {
        return '<p>No yacht data found in JSON response.</p>';
    }

    $console_script = '<script>
        console.log("===== CYA Single Yacht JSON =====");
        console.log(' . wp_json_encode( $data, JSON_PRETTY_PRINT ) . ');
        console.log("================================");
    </script>';

    $y = $data['yacht'];

    // ===== FIELD MAPPING (same as before) =====

    // Basic identity
    $name        = $y['yachtName']        ?? '';
    $type        = $y['yachtType']        ?? '';
    $prevName    = $y['yachtPreviousName']?? '';

    // Dimensions & capacity
    $lengthFt    = $y['sizeFeet']         ?? '';
    $lengthM     = $y['sizeMeter']        ?? '';
    $beam        = $y['yachtBeam']        ?? '';
    $draft       = $y['yachtDraft']       ?? '';
    $units       = $y['yachtUnits']       ?? '';
    $pax         = $y['yachtPax']         ?? '';
    $cabins      = $y['yachtCabins']      ?? '';
    $king        = $y['yachtKing']        ?? '';
    $queen       = $y['yachtQueen']       ?? '';
    $singleCab   = $y['yachtSingleCabins']?? '';
    $doubleCab   = $y['yachtDoubleCabins']?? '';
    $twinCab     = $y['yachtTwinCabins']  ?? '';
    $pullmanCab  = $y['yachtPullmanCabins'] ?? '';

    // Build info
    $builder     = $y['yachtBuilder']     ?? '';
    $yearBuilt   = $y['yachtYearBuilt']   ?? '';
    $refit       = $y['yachtRefit']       ?? '';
    $flag        = $y['yachtFlag']        ?? '';
    $homePort    = $y['yachtHomePort']    ?? '';
    $basePort    = $y['yachtWBasePort']   ?? '';

    // Performance / machinery
    $cruiseSpeed = $y['yachtCruiseSpeed'] ?? '';
    $maxSpeed    = $y['yachtMaxSpeed']    ?? '';
    $engines     = $y['yachtEngines']     ?? '';
    $fuelBurn    = $y['yachtFuel']        ?? '';
    $consUnits   = $y['yachtConsumptionUnits'] ?? '';
    $range       = $y['yachtRange']       ?? '';
    $generator   = $y['yachtGenerator']   ?? '';

    // Comfort & features
    $ac          = $y['yachtAc']          ?? '';
    $acNight     = $y['yachtAcNight']     ?? '';
    $acSurcharge = $y['yachtAcSurCharge'] ?? '';
    $helipad     = $y['yachtHelipad']     ?? '';
    $jacuzzi     = $y['yachtJacuzzi']     ?? '';
    $gym         = $y['yachtGym']         ?? '';
    $stabilizers = $y['yachtStabilizers'] ?? '';
    $elevators   = $y['yachtElevators']   ?? '';
    $wheelchair  = $y['yachtWheelChairAccess'] ?? '';
    $guestSmoke  = $y['yachtGuestSmoke']  ?? '';
    $children    = $y['yachtChildrenAllowed'] ?? '';
    $minChildAge = $y['yachtMinChildAge'] ?? '';

    // Electrical / utilities
    $voltages    = $y['yachtVoltages']    ?? '';
    $waterMaker  = $y['yachtWaterMaker']  ?? '';
    $waterCap    = $y['yachtWaterCapacity'] ?? '';
    $iceMaker    = $y['yachtIceMaker']    ?? '';

    // Prices
    $currency    = $y['yachtCurrencySymbol'] ?? '';
    $currencyCode= $y['yachtCurrency']   ?? '';
    $lowPrice    = $y['yachtLowPrice']   ?? '';
    $highPrice   = $y['yachtHighPrice']  ?? '';
    $terms       = $y['yachtTermsType']  ?? '';
    $priceDetails= $y['yachtPriceDetails'] ?? '';

    // Areas
    $summerArea  = $y['yachtSummerArea'] ?? '';
    $winterArea  = $y['yachtWinterArea'] ?? '';
    $locationDet = $y['yachtLocationDetails'] ?? '';

    // Long texts
    $accommodations = $y['yachtAccommodations'] ?? '';
    $desc1          = $y['yachtDesc1'] ?? '';
    $otherToys      = $y['yachtOtherToys'] ?? '';
    $otherEntertain = $y['yachtOtherEntertain'] ?? '';
    $communicate    = $y['yachtCommunicate'] ?? '';
    $sampleMenu     = $y['yachtSampleMenu'] ?? '';
    $brokerNotes    = $y['yachtBrokerNotes'] ?? '';

    // Water toys
    $adultSkis   = $y['yachtAdultWSkis'] ?? '';
    $kidsSkis    = $y['yachtKidsSkis']   ?? '';
    $waveRun     = $y['yachtWaveRun']    ?? '';
    $tube        = $y['yachtTube']       ?? '';
    $wakeBoard   = $y['yachtWakeBoard']  ?? '';
    $seaBob      = $y['yachtSeaBob']     ?? '';
    $seaScooter  = $y['yachtSeaScooter'] ?? '';
    $scubaOnboard= $y['yachtScubaOnboard'] ?? '';
    $licenseInfo = $y['yachtLicenseInfo'] ?? '';

    // Entertainment flags
    $satTv       = $y['yachtSatTv']      ?? '';
    $ipod        = $y['yachtIpod']       ?? '';
    $internet    = $y['yachtInternet']   ?? '';
    $sailInstruct= $y['yachtSailInstruct'] ?? '';

    // Crew
    $crewCount   = $y['yachtCrew']       ?? '';
    $crewSmoke   = $y['yachtCrewSmoke']  ?? '';
    $crewPets    = $y['yachtCrewPets']   ?? '';
    $crewProfile = $y['yachtCrewProfile'] ?? '';
    $captainName   = $y['yachtCaptainName'] ?? '';
    $captainNation = $y['yachtCaptainNation'] ?? '';
    $captainLang   = $y['yachtCaptainLang'] ?? '';

    // Links
    $brokerWeb   = $y['yachtBrokerWeb']  ?? '';
    $userWeb     = $y['yachtUserWeb']    ?? '';
    $layoutImg   = $y['yachtLayout']     ?? '';
    $fullEbro    = $y['yachtFullEbrochure'] ?? '';
    $fullRates   = $y['yachtFullRates']  ?? '';

    // Insurance / management
    $contractName = $y['yachtContractName'] ?? '';
    $termsContract= $y['yachtTerms'] ?? '';
    $insFlag      = $y['yachtInsFlag'] ?? '';
    $insHome      = $y['yachtInsHomeport'] ?? '';
    $manager      = $y['yachtManager'] ?? '';
    $managerPhone = $y['yachtManagerPhone'] ?? '';
    $managerEmail = $y['yachtManagerEmail'] ?? '';
    $conPhone1    = $y['yachtConPhone1'] ?? '';
    $conFax       = $y['yachtConFax'] ?? '';
    $conEmail1    = $y['yachtWaveConEmail'] ?? '';
    $conEmail2    = $y['yachtConOther'] ?? '';

    // Gallery pics
    $pics = array();
    for ( $i = 1; $i <= 19; $i++ ) {
        $picKey  = 'yachtPic' . $i;
        $descKey = 'yachtDesc' . $i;
        if ( ! empty( $y[ $picKey ] ) ) {
            $pics[] = array(
                'url'  => $y[ $picKey ],
                'desc' => $y[ $descKey ] ?? '',
            );
        }
    }

    $hero = ! empty( $pics[0]['url'] ) ? $pics[0]['url'] : 'https://via.placeholder.com/1200x600?text=Yacht';

    // MAIN WRAPPER – dark theme
    $html  = '<div class="cya-yacht-ebrochure" style="max-width:100%;width:100%;margin:0 auto;background:#000000;color:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',system-ui,sans-serif;padding-bottom:40px;">';

    // HERO
    $html .= '<div id="cya-yacht-ebrochure1" class="cya-yacht-hero" style="position:relative;margin-bottom:32px;overflow:hidden;border-bottom:1px solid rgba(211,175,55,0.35);">';
    $html .= '  <img src="' . esc_url( $hero ) . '" alt="' . esc_attr( $name ) . '" style="width:100%;height:520px;display:block;object-fit:cover;filter:brightness(0.85);">';
    $html .= '  <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,0,0,0.45),rgba(0,41,26,0.45));"></div>';
    $html .= '  <div style="position:absolute;left:50%;bottom:32px;transform:translateX(-50%);width:90%;max-width:1600px;padding:20px 24px;border-radius:14px;border:1px solid rgba(211,175,55,0.6);background:linear-gradient(145deg,rgba(0,0,0,0.92),rgba(0,41,26,0.95));box-shadow:0 18px 45px rgba(0,0,0,0.8);backdrop-filter:blur(10px);">';
    $html .= '    <h1 style="margin:0;font-size:30px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#D3AF37;">' . esc_html( $name ) . '</h1>';

    $heroSub = array();
    if ( opus_cya_has_value( $type ) )      $heroSub[] = $type;
    if ( opus_cya_has_value( $lengthFt ) )  $heroSub[] = $lengthFt;
    if ( opus_cya_has_value( $lengthM ) )   $heroSub[] = $lengthM;

    if ( ! empty( $heroSub ) ) {
        $html .= '    <p style="margin:6px 0 0;font-size:14px;color:#e0e0e0;opacity:0.9;">' . esc_html( implode( ' • ', $heroSub ) ) . '</p>';
    }

    if ( opus_cya_has_value( $prevName ) ) {
        $html .= '    <p style="margin:4px 0 0;font-size:12px;color:#b0b0b0;">Previous Name: <span style="color:#D3AF37;">' . esc_html( $prevName ) . '</span></p>';
    }

    $html .= '  </div>';
    $html .= '</div>';

    // TOP META CARDS
    $html .= '<div id="cya-yacht-ebrochure1" class="cya-yacht-meta" style="display:flex;flex-wrap:wrap;gap:18px;width:90%;margin:24px auto 32px auto;max-width:1600px;">';

    // COMMON CARD STYLE
    $card_base = 'flex:1 1 260px;border-radius:16px;padding:18px 18px 20px 18px;background:radial-gradient(circle at top,#00291A,#000000);border:1px solid rgba(211,175,55,0.7);box-shadow:0 14px 38px rgba(0,0,0,0.85);';

    // Key Specs
    $html .= '  <div id="cya-yacht-ebrochure1" class="box1" style="' . $card_base . '">';
    $html .= '    <h3 style="margin:0 0 10px;font-size:20px;font-weight:600;color:#D3AF37;letter-spacing:0.04em;text-transform:uppercase;">Key Specs</h3>';
    $html .= '    <ul style="list-style:none;padding:0;margin:0;font-size:14px;line-height:1.7;color:#f0f0f0;">';
    $html .= opus_cya_li( 'Length (ft)',    $lengthFt );
    $html .= opus_cya_li( 'Length (m)',     $lengthM );
    $html .= opus_cya_li( 'Beam',          $beam );
    $html .= opus_cya_li( 'Draft',         $draft );
    $html .= opus_cya_li( 'Units',         $units );
    $html .= opus_cya_li( 'Guests',        $pax );
    $html .= opus_cya_li( 'Total Cabins',  $cabins );
    if ( opus_cya_has_value( $king ) || opus_cya_has_value( $queen ) || opus_cya_has_value( $singleCab ) || opus_cya_has_value( $doubleCab ) || opus_cya_has_value( $twinCab ) || opus_cya_has_value( $pullmanCab ) ) {
        $beds = array();
        if ( opus_cya_has_value( $king ) )       $beds[] = $king . ' King';
        if ( opus_cya_has_value( $queen ) )      $beds[] = $queen . ' Queen';
        if ( opus_cya_has_value( $singleCab ) )  $beds[] = $singleCab . ' Single';
        if ( opus_cya_has_value( $doubleCab ) )  $beds[] = $doubleCab . ' Double';
        if ( opus_cya_has_value( $twinCab ) )    $beds[] = $twinCab . ' Twin';
        if ( opus_cya_has_value( $pullmanCab ) ) $beds[] = $pullmanCab . ' Pullman';
        $html .= '<li><strong style="color:#D3AF37;">Bed Layout:</strong> ' . esc_html( implode( ', ', $beds ) ) . '</li>';
    }
    $html .= opus_cya_li( 'Builder',      $builder );
    $html .= opus_cya_li( 'Year Built',   $yearBuilt );
    $html .= opus_cya_li( 'Refit',        $refit );
    $html .= opus_cya_li( 'Flag',         $flag );
    $html .= opus_cya_li( 'Home Port',    $homePort );
    $html .= opus_cya_li( 'Base Port',    $basePort );
    $html .= '    </ul>';
    $html .= '  </div>';

    // Charter Rates
    $html .= '  <div class="box1" style="' . $card_base . '">';
    $html .= '    <h3 style="margin:0 0 10px;font-size:20px;font-weight:600;color:#D3AF37;letter-spacing:0.04em;text-transform:uppercase;">Charter Rates</h3>';
    $html .= '    <ul style="list-style:none;padding:0;margin:0;font-size:14px;line-height:1.7;color:#f0f0f0;">';

    if ( opus_cya_has_value( $lowPrice ) || opus_cya_has_value( $highPrice ) ) {
        $priceText = '';
        if ( opus_cya_has_value( $lowPrice ) && opus_cya_has_value( $highPrice ) && $lowPrice !== $highPrice ) {
            $priceText = $lowPrice . ' - ' . $highPrice;
        } else {
            $priceText = ( $lowPrice ?: $highPrice );
        }
        $html .= opus_cya_li( 'Price Range', $priceText );
    }

    $html .= opus_cya_li( 'Currency',       $currency );
    $html .= opus_cya_li( 'Currency Code',  $currencyCode );
    $html .= opus_cya_li( 'Terms Type',     $terms );
    $html .= opus_cya_li( 'Contract (MYBA etc.)', $termsContract );

    $html .= '    </ul>';

    if ( opus_cya_has_value( $priceDetails ) ) {
        $html .= '<div style="margin-top:8px;font-size:12px;line-height:1.6;color:#d0d0d0;">'
              . nl2br( esc_html( $priceDetails ) ) . '</div>';
    }

    $html .= '  </div>';

    // Cruising Areas
    $html .= '  <div id="cya-yacht-ebrochure1" class="box1" style="' . $card_base . '">';
    $html .= '    <h3 style="margin:0 0 10px;font-size:20px;font-weight:600;color:#D3AF37;letter-spacing:0.04em;text-transform:uppercase;">Cruising Areas</h3>';
    $html .= '    <ul style="list-style:none;padding:0;margin:0;font-size:14px;line-height:1.7;color:#f0f0f0;">';
    $html .= opus_cya_li( 'Summer',  $summerArea );
    $html .= opus_cya_li( 'Winter',  $winterArea );
    $html .= opus_cya_li( 'Details', $locationDet );
    $html .= '    </ul>';
    $html .= '  </div>';

    $html .= '</div>'; // meta row

    // COMMON SECTION WRAPPER STYLE
    $section_base = 'width:90%;margin:50px auto !important;max-width:1600px !important;background:radial-gradient(circle at top left,#00291A,#000000);border-radius:18px;padding:22px 22px 26px;border:1px solid rgba(211,175,55,0.45);box-shadow:0 20px 50px rgba(0,0,0,0.9);';

    $section_title_style = 'font-size:22px;margin:0 0 10px;color:#D3AF37;letter-spacing:0.05em;text-transform:uppercase;font-weight:600;';

    $list_style = 'list-style:none;padding:0;margin:0;font-size:15px;line-height:1.7;color:#f5f5f5;';

    // Performance & Machinery
    if (
        opus_cya_has_value( $cruiseSpeed ) ||
        opus_cya_has_value( $maxSpeed ) ||
        opus_cya_has_value( $engines ) ||
        opus_cya_has_value( $fuelBurn ) ||
        opus_cya_has_value( $consUnits ) ||
        opus_cya_has_value( $range ) ||
        opus_cya_has_value( $generator )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" class="testsection" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Performance & Machinery</h2>';
        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Cruising Speed',   $cruiseSpeed );
        $html .= opus_cya_li( 'Maximum Speed',    $maxSpeed );
        $html .= opus_cya_li( 'Engines',          $engines );
        if ( opus_cya_has_value( $fuelBurn ) || opus_cya_has_value( $consUnits ) ) {
            $fuelText = trim( $fuelBurn . ' ' . $consUnits );
            $html .= opus_cya_li( 'Fuel Consumption', $fuelText );
        }
        $html .= opus_cya_li( 'Range',           $range );
        $html .= opus_cya_li( 'Generators',      $generator );
        $html .= '  </ul>';
        $html .= '</section>';
    }

    // Comfort & Accessibility
    if (
        opus_cya_has_value( $ac ) ||
        opus_cya_has_value( $acNight ) ||
        opus_cya_has_value( $acSurcharge ) ||
        opus_cya_has_value( $helipad ) ||
        opus_cya_has_value( $jacuzzi ) ||
        opus_cya_has_value( $gym ) ||
        opus_cya_has_value( $stabilizers ) ||
        opus_cya_has_value( $elevators ) ||
        opus_cya_has_value( $wheelchair ) ||
        opus_cya_has_value( $guestSmoke ) ||
        opus_cya_has_value( $children ) ||
        opus_cya_has_value( $minChildAge )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Comfort & Accessibility</h2>';
        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Air Conditioning', $ac );
        $html .= opus_cya_li( 'AC at Night',      $acNight );
        $html .= opus_cya_li( 'AC Surcharge',     $acSurcharge );
        $html .= opus_cya_li( 'Helipad',          $helipad );
        $html .= opus_cya_li( 'Jacuzzi',          $jacuzzi );
        $html .= opus_cya_li( 'Gym',              $gym );
        $html .= opus_cya_li( 'Stabilizers',      $stabilizers );
        $html .= opus_cya_li( 'Elevators',        $elevators );
        $html .= opus_cya_li( 'Wheelchair Access', $wheelchair );
        $html .= opus_cya_li( 'Guest Smoking Policy', $guestSmoke );
        $html .= opus_cya_li( 'Children Allowed', $children );
        $html .= opus_cya_li( 'Minimum Child Age', $minChildAge );
        $html .= '  </ul>';
        $html .= '</section>';
    }

    // Utilities
    if (
        opus_cya_has_value( $voltages ) ||
        opus_cya_has_value( $waterMaker ) ||
        opus_cya_has_value( $waterCap ) ||
        opus_cya_has_value( $iceMaker )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Utilities</h2>';
        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Voltages',        $voltages );
        $html .= opus_cya_li( 'Water Maker',     $waterMaker );
        $html .= opus_cya_li( 'Water Capacity',  $waterCap );
        $html .= opus_cya_li( 'Ice Maker',       $iceMaker );
        $html .= '  </ul>';
        $html .= '</section>';
    }

    // Accommodations
    if ( opus_cya_has_value( $accommodations ) ) {
        $html .= '<section style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Accommodations</h2>';
        $html .= '  <div style="font-size:15px;line-height:1.8;color:#f5f5f5;">' . $accommodations . '</div>';
        $html .= '</section>';
    }

    if ( opus_cya_has_value( $desc1 ) ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Yacht Description</h2>';
        $html .= '  <div style="font-size:15px;line-height:1.8;color:#f5f5f5;">' . $desc1 . '</div>';
        $html .= '</section>';
    }

    // Water Toys & Tenders
    if (
        opus_cya_has_value( $otherToys ) ||
        opus_cya_has_value( $adultSkis ) ||
        opus_cya_has_value( $kidsSkis ) ||
        opus_cya_has_value( $waveRun ) ||
        opus_cya_has_value( $tube ) ||
        opus_cya_has_value( $wakeBoard ) ||
        opus_cya_has_value( $seaBob ) ||
        opus_cya_has_value( $seaScooter )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Water Toys & Tenders</h2>';

        if ( opus_cya_has_value( $otherToys ) ) {
            $html .= '  <div style="font-size:15px;line-height:1.8;margin-bottom:10px;color:#f5f5f5;">' . $otherToys . '</div>';
        }

        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Adult Water Skis', $adultSkis );
        $html .= opus_cya_li( 'Kids Skis',        $kidsSkis );
        $html .= opus_cya_li( 'Wave Runner',      $waveRun );
        $html .= opus_cya_li( 'Tubes',            $tube );
        $html .= opus_cya_li( 'Wakeboard',        $wakeBoard );
        $html .= opus_cya_li( 'Seabob',           $seaBob );
        $html .= opus_cya_li( 'Sea Scooter',      $seaScooter );
        $html .= '  </ul>';

        $html .= '</section>';
    }

    // Diving
    if (
        opus_cya_has_value( $scubaOnboard ) ||
        opus_cya_has_value( $licenseInfo )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Diving</h2>';
        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Scuba Onboard', $scubaOnboard );
        $html .= opus_cya_li( 'License Info',  $licenseInfo );
        $html .= '  </ul>';
        $html .= '</section>';
    }

    // Entertainment & Communications
    if (
        opus_cya_has_value( $otherEntertain ) ||
        opus_cya_has_value( $communicate ) ||
        opus_cya_has_value( $satTv ) ||
        opus_cya_has_value( $ipod ) ||
        opus_cya_has_value( $internet ) ||
        opus_cya_has_value( $sailInstruct )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Entertainment & Communications</h2>';

        if ( opus_cya_has_value( $otherEntertain ) ) {
            $html .= '  <div style="font-size:15px;line-height:1.8;margin-bottom:10px;color:#f5f5f5;">' . $otherEntertain . '</div>';
        }
        if ( opus_cya_has_value( $communicate ) ) {
            $html .= '  <div style="font-size:15px;line-height:1.8;margin-bottom:10px;color:#f5f5f5;">' . $communicate . '</div>';
        }

        $html .= '  <ul style="' . $list_style . '">';
        $html .= opus_cya_li( 'Satellite TV',  $satTv );
        $html .= opus_cya_li( 'iPod / Audio',  $ipod );
        $html .= opus_cya_li( 'Internet',      $internet );
        $html .= opus_cya_li( 'Sail Instruction', $sailInstruct );
        $html .= '  </ul>';

        $html .= '</section>';
    }

    // Crew
    if (
        opus_cya_has_value( $crewCount ) ||
        opus_cya_has_value( $crewSmoke ) ||
        opus_cya_has_value( $crewPets ) ||
        opus_cya_has_value( $captainName ) ||
        opus_cya_has_value( $captainNation ) ||
        opus_cya_has_value( $captainLang ) ||
        opus_cya_has_value( $crewProfile )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Crew</h2>';
        $html .= '  <ul style="' . $list_style . 'margin-bottom:8px;">';
        $html .= opus_cya_li( 'Total Crew',      $crewCount );
        $html .= opus_cya_li( 'Crew Smoking',    $crewSmoke );
        $html .= opus_cya_li( 'Crew Pets',       $crewPets );
        $html .= opus_cya_li( 'Captain',         $captainName );
        $html .= opus_cya_li( 'Captain Nationality', $captainNation );
        $html .= opus_cya_li( 'Captain Languages',   $captainLang );
        $html .= '  </ul>';

        if ( opus_cya_has_value( $crewProfile ) ) {
            $html .= '  <div style="font-size:15px;line-height:1.8;color:#f5f5f5;">' . $crewProfile . '</div>';
        }

        $html .= '</section>';
    }

    // Layout & Links
    if (
        opus_cya_has_value( $layoutImg ) ||
        opus_cya_has_value( $brokerWeb ) ||
        opus_cya_has_value( $userWeb ) ||
        opus_cya_has_value( $fullEbro ) ||
        opus_cya_has_value( $fullRates )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Layout & External Links</h2>';

        if ( opus_cya_has_value( $layoutImg ) ) {
            $html .= '<div style="margin-bottom:14px;border-radius:14px;overflow:hidden;border:1px solid rgba(211,175,55,0.5);">';
            $html .= '  <img src="' . esc_url( $layoutImg ) . '" alt="Yacht Layout" style="max-width:100%;height:auto;display:block;">';
            $html .= '</div>';
        }

        $html .= '<ul style="' . $list_style . '">';

        $link_style = 'text-decoration:none;border-radius:999px;border:1px solid rgba(211,175,55,0.7);padding:6px 14px;display:inline-block;margin:4px 0;background:rgba(0,41,26,0.85);color:#f5f5f5;transition:all .2s ease;';
        $link_hover = "this.style.backgroundColor='rgba(211,175,55,0.18)';this.style.color='#D3AF37';";
        $link_out   = "this.style.backgroundColor='rgba(0,41,26,0.85)';this.style.color='#f5f5f5';";

        if ( opus_cya_has_value( $brokerWeb ) ) {
            $html .= '<li><a href="' . esc_url( $brokerWeb ) . '" target="_blank" rel="noopener" style="' . $link_style . '" onmouseover="'.$link_hover.'" onmouseout="'.$link_out.'">Broker Website</a></li>';
        }
        if ( opus_cya_has_value( $userWeb ) ) {
            $html .= '<li><a href="' . esc_url( $userWeb ) . '" target="_blank" rel="noopener" style="' . $link_style . '" onmouseover="'.$link_hover.'" onmouseout="'.$link_out.'">Online Brochure</a></li>';
        }
        if ( opus_cya_has_value( $fullEbro ) ) {
            $html .= '<li><a href="' . esc_url( $fullEbro ) . '" target="_blank" rel="noopener" style="' . $link_style . '" onmouseover="'.$link_hover.'" onmouseout="'.$link_out.'">Full E-Brochure</a></li>';
        }
        if ( opus_cya_has_value( $fullRates ) ) {
            $html .= '<li><a href="' . esc_url( $fullRates ) . '" target="_blank" rel="noopener" style="' . $link_style . '" onmouseover="'.$link_hover.'" onmouseout="'.$link_out.'">Full Rate Sheet</a></li>';
        }

        $html .= '</ul>';
        $html .= '</section>';
    }

    // Insurance & Management
    if (
        opus_cya_has_value( $contractName ) ||
        opus_cya_has_value( $insFlag ) ||
        opus_cya_has_value( $insHome ) ||
        opus_cya_has_value( $manager ) ||
        opus_cya_has_value( $managerPhone ) ||
        opus_cya_has_value( $managerEmail ) ||
        opus_cya_has_value( $conPhone1 ) ||
        opus_cya_has_value( $conFax ) ||
        opus_cya_has_value( $conEmail1 ) ||
        opus_cya_has_value( $conEmail2 ) ||
        opus_cya_has_value( $brokerNotes )
    ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Insurance & Management</h2>';

        $html .= '  <ul style="' . $list_style . 'font-size:14px;margin-bottom:10px;">';
        $html .= opus_cya_li( 'Contract',            $contractName );
        $html .= opus_cya_li( 'Insurance Flag',      $insFlag );
        $html .= opus_cya_li( 'Insurance Homeport',  $insHome );
        $html .= opus_cya_li( 'Manager',             $manager );
        $html .= opus_cya_li( 'Manager Phone',       $managerPhone );
        $html .= opus_cya_li( 'Manager Email',       $managerEmail );
        $html .= opus_cya_li( 'Contact Phone',       $conPhone1 );
        $html .= opus_cya_li( 'Contact Fax',         $conFax );
        $html .= opus_cya_li( 'Contact Email 1',     $conEmail1 );
        $html .= opus_cya_li( 'Contact Email 2',     $conEmail2 );
        $html .= '  </ul>';

        if ( opus_cya_has_value( $brokerNotes ) ) {
            $html .= '  <div style="font-size:14px;line-height:1.8;color:#f5f5f5;">' . $brokerNotes . '</div>';
        }

        $html .= '</section>';
    }

    // Gallery
    if ( ! empty( $pics ) ) {
        $html .= '<section id="cya-yacht-ebrochure1" style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Photo Gallery</h2>';
        $html .= '  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">';

        foreach ( $pics as $pic ) {
            $alt = ! empty( $pic['desc'] ) ? $pic['desc'] : $name;
            $html .= '<figure style="margin:0;border-radius:14px;overflow:hidden;border:1px solid rgba(211,175,55,0.4);background:#000000;">';
            $html .= '  <img src="' . esc_url( $pic['url'] ) . '" alt="' . esc_attr( $alt ) . '" style="width:100%;height:210px;object-fit:cover;display:block;transition:transform .3s ease,filter .3s ease;filter:brightness(0.95);" onmouseover="this.style.transform=\'scale(1.04)\';this.style.filter=\'brightness(1)\';" onmouseout="this.style.transform=\'scale(1)\';this.style.filter=\'brightness(0.95)\';">';
            if ( opus_cya_has_value( $pic['desc'] ) ) {
                $html .= '  <figcaption style="padding:8px 10px;font-size:12px;color:#e0e0e0;">' . esc_html( $pic['desc'] ) . '</figcaption>';
            }
            $html .= '</figure>';
        }

        $html .= '  </div>';
        $html .= '</section>';
    }

    // Additional Technical Details
    $used_keys = array(
        'yachtId','yachtName','yachtPreviousName','yachtLogo','yachtType','yachtLength','yachtPowerCat',
        'sizeFeet','sizeMeter','yachtBeam','yachtDraft','yachtUnits','yachtPax','yachtCabins','yachtKing',
        'yachtQueen','yachtSingleCabins','yachtDoubleCabins','yachtTwinCabins','yachtPullmanCabins','yachtRefit',
        'yachtHelipad','yachtJacuzzi','yachtGym','yachtStabilizers','yachtElevators','yachtWheelChairAccess',
        'yachtAc','yachtPrefPickUp','yachtOtherPickUp','yachtTurnAround','yachtYearBuilt','yachtBuilder',
        'yachtBrokerWeb','yachtUserWeb','yachtVideoUrl','yachtV360Url','yachtCruiseSpeed','yachtMaxSpeed',
        'yachtAccommodations','yachtHighPrice','yachtLowPrice','yachtHighNumericPrice','yachtLowNumericPrice',
        'yachtCurrencySymbol','yachtCurrency','yachtPriceDetails','yachtTermsType','yachtTermsTypeNum',
        'yachtVcrDvd','yachtSalonStereo','yachtBoardGames','yachtCamCorder','yachtNumDineIn','yachtDeckShower',
        'yachtSpecialDiets','yachtKosher','yachtBBQ','yachtGayCharters','yachtNudeCharters','yachtHairDryer',
        'yachtGuestSmoke','yachtGuestPet','yachtChildrenAllowed','yachtMinChildAge','yachtGenerator','yachtEngines',
        'yachtFuel','yachtInverter','yachtVoltages','yachtWaterMaker','yachtWaterCapacity','yachtIceMaker',
        'yachtDinghy','yachtDinghyHp','yachtDinghyPax','yachtAdultWSkis','yachtKidsSkis','yachtJetSkis',
        'yachtWaveRun','yachtKneeBoard','yachtStandUpPaddle','yachtWindSurf','yachtGearSnorkel','yachtTube',
        'yachtScurfer','yachtWakeBoard','yacht1ManKayak','yacht2ManKayak','yachtSeaBob','yachtSeaScooter',
        'yachtKiteBoarding','yachtKiteBoardingDetails','yachtFishPermit','yachtFloatMats','yachtSwimPlatform',
        'yachtBoardingLadder','yachtDinghySailing','yachtGamesBeach','yachtFishingGear','yachtFishGearType',
        'yachtNumFishRods','yachtUnderWaterCam','yachtUnderWaterVideo','yachtGreenMakeWater',
        'yachtGreenReuseBottle','yachtGreenOther','yachtScubaOnboard','yachtResortCourse','yachtFullCourse',
        'yachtLicenseInfo','yachtCompressor','yachtNumDiveTanks','yachtNumBCS','yachtNumRegs','yachtNumWetSuits',
        'yachtNumWeights','yachtNumDivers','yachtNumDives','yachtNumNightDives','yachtNumDiveLights',
        'yachtDiveInfo','yachtDiveCosts','yachtPic1','yachtDesc1','yachtPic2','yachtDesc2','yachtPic3','yachtDesc3',
        'yachtPic4','yachtDesc4','yachtPic5','yachtDesc5','yachtPic6','yachtDesc6','yachtPic7','yachtDesc7',
        'yachtPic8','yachtDesc8','yachtPic9','yachtDesc9','yachtPic10','yachtDesc10','yachtPic11','yachtDesc11',
        'yachtPic12','yachtDesc12','yachtPic13','yachtDesc13','yachtPic14','yachtDesc14','yachtPic15','yachtDesc15',
        'yachtPic16','yachtDesc16','yachtPic17','yachtDesc17','yachtPic18','yachtDesc18','yachtPic19','yachtDesc19',
        'yachtLayout','yachtFullEbrochure','yachtFullRates','yachtOtherToys','yachtOtherEntertain',
        'yachtCommunicate','yachtSummerArea','yachtWinterArea','yachtShowers','yachtWashBasins','yachtHeads',
        'yachtElectricHeads','yachtTpInHeads','yachtSampleMenu','yachtMenu1Pic','yachtMenu2Pic','yachtMenu3Pic',
        'yachtMenu4Pic','yachtMenu5Pic','yachtMenu6Pic','yachtMenu7Pic','yachtMenu8Pic','yachtMenu9Pic',
        'yachtMenu10Pic','yachtCrew','yachtCrewSmoke','yachtCrewPets','yachtCrewPetType','yachtCaptainName',
        'yachtCaptainNation','yachtCaptainBorn','yachtCaptainLic','yachtCaptainYrSail','yachtCaptainYrChart',
        'yachtCaptainLang','yachtCrewName','yachtCrewTitle','yachtCrewNation','yachtCrewYrBorn','yachtCrewLic',
        'yachtCrewYrSail','yachtCrewYrChart','yachtCrewLang','yachtCrewProfile','yachtCrewPhoto','yachtCrew1Pic',
        'yachtCrew2Pic','yachtCrew3Pic','yachtCrew4Pic','yachtCrew5Pic','yachtCrew6Pic','yachtCrew7Pic',
        'yachtCrew8Pic','yachtCrew9Pic','yachtCrew10Pic','yachtCrew1Name','yachtCrew2Name','yachtCrew3Name',
        'yachtCrew4Name','yachtCrew5Name','yachtCrew6Name','yachtCrew7Name','yachtCrew8Name','yachtCrew9Name',
        'yachtCrew10Name','yachtCrew1Title','yachtCrew2Title','yachtCrew3Title','yachtCrew4Title','yachtCrew5Title',
        'yachtCrew6Title','yachtCrew7Title','yachtCrew8Title','yachtCrew9Title','yachtCrew10Title','yachtWBasePort',
        'yachtRig','yachtGrossTons','yachtAcNight','yachtAcSurCharge','yachtTubs','yachtLocationDetails',
        'yachtTerms','yachtCaptOnly','yachtSpecialCon','yachtContracts','yachtConsumptionUnits','yachtRange',
        'yachtPermit','yachtLicense','yachtMca','yachtDeepSeaFish','yachtSatTv','yachtIpod','yachtVideo',
        'yachtSailInstruct','yachtInternet','yachtCaptainOnly','yachtBrokerNotes','yachtInsCompany','yachtPolicy',
        'yachtLiability','yachtEffectiveDate','yachtContractName','yachtContractAddress','yachtCoverageAreas',
        'yachtInsFlag','yachtInsHomeport','yachtRegNum','yachtConPhone1','yachtConPhone2','yachtConPhone3',
        'yachtConFax','yachtWaveConEmail','yachtConOther','yachtManager','yachtManagerName','yachtManagerPhone',
        'yachtManagerToll','yachtManagerEmail'
    );

    $extraRows = array();
    foreach ( $y as $key => $val ) {
        if ( in_array( $key, $used_keys, true ) ) {
            continue;
        }
        if ( ! opus_cya_has_value( $val ) ) {
            continue;
        }
        $extraRows[] = array(
            'key'   => $key,
            'value' => $val,
        );
    }

    if ( ! empty( $extraRows ) ) {
        $html .= '<section style="' . $section_base . '">';
        $html .= '  <h2 style="' . $section_title_style . '">Additional Technical Details</h2>';
        $html .= '  <table style="width:100%;border-collapse:collapse;font-size:13px;background:rgba(0,0,0,0.75);border-radius:12px;overflow:hidden;">';
        $html .= '    <tbody>';
        foreach ( $extraRows as $row ) {
            $html .= '      <tr>';
            $html .= '        <td style="border:1px solid rgba(211,175,55,0.5);padding:7px 10px;font-weight:600;font-size:14px;width:35%;color:#D3AF37;background:rgba(0,41,26,0.9);">'
                   . esc_html( $row['key'] ) . '</td>';
            $html .= '        <td style="border:1px solid rgba(211,175,55,0.35);padding:7px 10px;color:#f5f5f5;background:rgba(0,0,0,0.85);">'
                   . wp_kses_post( nl2br( esc_html( $row['value'] ) ) ) . '</td>';
            $html .= '      </tr>';
        }
        $html .= '    </tbody>';
        $html .= '  </table>';
        $html .= '</section>';
    }

    $json_pretty = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    $html .= '<details style="margin-top:24px;display:none;">';
    $html .= '  <summary style="cursor:pointer;font-size:14px;font-weight:600;color:#D3AF37;">Developer: Raw JSON response</summary>';
    $html .= '  <pre style="white-space:pre-wrap;font-size:12px;background:#111;color:#0f0;padding:12px;border-radius:6px;max-height:400px;overflow:auto;margin-top:8px;">'
          . esc_html( $json_pretty )
          . '</pre>';
    $html .= '</details>';

    $html .= $console_script;
    $html .= '</div>';

    return $html;
}


/**
 * SHORTCODE 3: page wrapper
 * [cya_yacht_page]
 * Uses ?yid=xxxx from URL
 */
function opus_cya_yacht_page_shortcode( $atts ) {
    $yid = isset( $_GET['yid'] ) ? sanitize_text_field( wp_unslash( $_GET['yid'] ) ) : '';

    if ( empty( $yid ) ) {
        return '<p>No yacht selected.</p>';
    }

    $debug = ! empty( $_GET['debug'] ) ? '1' : '';

    return opus_cya_yacht_ebrochure_shortcode( array(
        'id'    => $yid,
        'debug' => $debug,
    ) );
}
add_shortcode( 'cya_yacht_page', 'opus_cya_yacht_page_shortcode' );

/**
 * 🔹 AJAX-style pagination (front-end only)
 */
add_action( 'wp_footer', 'opus_cya_ajax_pagination_script' );
function opus_cya_ajax_pagination_script() {
    ?>
    <script>
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.cya-page-link');total_pages 
        if (!link) return;

        e.preventDefault();

        const url = link.href;
        const wrapper = document.querySelector('#cya-yachts-wrapper');
        if (!wrapper) {
            window.location.href = url;
            return;
        }

        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (res) { return res.text(); })
        .then(function (html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newWrapper = doc.querySelector('#cya-yachts-wrapper');
            if (newWrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
                wrapper.style.opacity = '1';
                wrapper.style.pointerEvents = 'auto';

                window.scrollTo({ top: wrapper.offsetTop - 100, behavior: 'smooth' });
            } else {
                window.location.href = url;
            }
        })
        .catch(function () {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
            window.location.href = url;
        });
    });
    </script>
    <?php
}
