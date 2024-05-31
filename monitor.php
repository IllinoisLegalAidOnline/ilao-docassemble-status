<?php
if(isset($_GET['url'])) {
	header('Content-type: application/json');
	header("Content-Security-Policy: default-src 'self'");
	//print_r('{"status":"'.substr(get_headers($_GET['url'])[0], 9, 3).'"}');
	print_r(substr(get_headers($_GET['url'])[0], 9, 3));
}
else {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>ILAO Status Monitor</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Status Monitor for ILAO.">
    <meta name="robots" content="noindex">

    <style>
      .http-status {
        padding: 0;
        margin: 0;
        list-style: none;
        border: 1px solid silver;
        -ms-box-orient: horizontal;
        display: flex;
        flex-wrap: wrap;
      }
      .http-status > li {
        padding: 5px;
        width: calc(50% - 12px);
        min-width: 400px;
        margin: 1px;
        line-height: 1em;
        color: #000000;
        font-family: sans-serif;
        font-size: 1em;
        text-align: left;
        word-wrap: break-word;
      }
      .http-status span:first-child {
        float: right;
        border: 1px solid;
        background-color: #0002;
        border-radius: 3px;
        padding: 0px 1px;
        font-size: 0.75rem;
        line-height: 0.75rem;
      }
      .http-status span:nth-child(2) {
        text-align: right;
        display: inline-block;
        min-width: 1.5rem;
        padding-right: .5rem;
      }
      .http-status a { color: #000000; }
      .status-non { background: #aaaaaa; }  
      .status-def { background: #ff6e3a; }  
      .status-200 { background: #00d302; }  
      .status-308 { background: #00c2f9; }  
      .status-bad { background: #666666; }  
    </style>

    <script>
    ilio = {
      serverSide: true,
      pingInterval: 0, // Seconds
      pingDelay: 500, // Milliseconds
      maxActive: 3,
      urls: [
        'https://easyforms.illinoislegalaid.org/start/Appearance/appearance',
        'https://easyforms.illinoislegalaid.org/start/AppearanceEfile/appearance',
        'https://easyforms.illinoislegalaid.org/start/AskDebtCollectorStopContact/stop_debt_collection',
        'https://easyforms.illinoislegalaid.org/start/CannabisExpungement/cannabis_expungement',
        'https://easyforms.illinoislegalaid.org/start/CollectionProofLetter/collection_proof_debtor',
        'https://easyforms.illinoislegalaid.org/start/EFilingExemptionAppellateCourt/e-filing_exemption_appellate_court',
        'https://easyforms.illinoislegalaid.org/start/EfilingExemptionCircuitCourt/e-filing_exemption',
        'https://easyforms.illinoislegalaid.org/start/EFilingExemptionSupremeCourt/e-filing_exemption_supreme_court',
        'https://easyforms.illinoislegalaid.org/start/EmergencyOrderOfProtectionCookCounty/order_of_protection',
        'https://easyforms.illinoislegalaid.org/start/EndLockout/end_lockout_letter',
        'https://easyforms.illinoislegalaid.org/start/EvictATenant/evict_a_tenant',
        'https://easyforms.illinoislegalaid.org/start/ExtendTimeOfEviction/extend_time_of_eviction',
        'https://easyforms.illinoislegalaid.org/start/FeeWaiver/fee_waiver',
        'https://easyforms.illinoislegalaid.org/start/IDHRHousingDiscriminationComplaint/housing_discrimination_complaint',
        'https://easyforms.illinoislegalaid.org/start/InterpreterRequest/interpreter_request',
        'https://easyforms.illinoislegalaid.org/start/LivingWill/living_will',
        'https://easyforms.illinoislegalaid.org/start/Motion/motion',
        'https://easyforms.illinoislegalaid.org/start/NameChangeAdult/name_change_adult',
        'https://easyforms.illinoislegalaid.org/start/NameChangeChild/name_change_child',
        'https://easyforms.illinoislegalaid.org/start/OrderOfProtection/order_of_protection',
        'https://easyforms.illinoislegalaid.org/start/PersonnelFileRequest/personnel_file_request',
        'https://easyforms.illinoislegalaid.org/start/PowerOfAttorneyHealthCare/power_of_attorney_for_health_care',
        'https://easyforms.illinoislegalaid.org/start/PowerOfAttorneyProperty/power_of_attorney_for_property',
        'https://easyforms.illinoislegalaid.org/start/PowerOfAttorneyResignation/power_of_attorney_resignation',
        'https://easyforms.illinoislegalaid.org/start/PowerOfAttorneyRevocation/power_of_attorney_revocation',
        'https://easyforms.illinoislegalaid.org/start/ProofOfDelivery/proof_of_delivery',
        'https://easyforms.illinoislegalaid.org/start/RemoveEvictionFromPublicRecord/remove_eviction_from_record',
        'https://easyforms.illinoislegalaid.org/start/RequestTimeOffWorkDueToDomesticViolence/request_time_off_work_due_to_domestic_violence',
        'https://easyforms.illinoislegalaid.org/start/RespondToALawsuit/respond_to_a_lawsuit',
        'https://easyforms.illinoislegalaid.org/start/RespondToAnEviction/respond_to_an_eviction',
        'https://easyforms.illinoislegalaid.org/start/SecurityDepositDemand/security_deposit_demand_letter',
        'https://easyforms.illinoislegalaid.org/start/ShortTermGuardianship/short_term_guardianship',
        'https://easyforms.illinoislegalaid.org/start/SmallClaimsComplaint/small_claims_complaint',
        'https://easyforms.illinoislegalaid.org/start/StopWageAssignment/stop_wage_assignment',
        'https://easyforms.illinoislegalaid.org/start/TODI/transfer_on_death_instrument',
        'https://easyforms.illinoislegalaid.org/start/USCISApplications/main',
        'https://easyforms.illinoislegalaid.org/start/VoluntaryAcknowledgementOfParentage/voluntary_acknowledgement_of_paternity',
        'https://easyforms.illinoislegalaid.org/start/WhichProtectiveOrderIsRightForMe/which_protective_order',
      ],
      init: () => {
        var ul = document.getElementById("status-list");
        ilio.urls.forEach((url,i) => { ul.appendChild(ilio.makeLi(url,i+1)); ul.appendChild(ilio.makeLi(url+'/#/1')); });
        ilio.pingUrls();
        if(ilio.pingInterval) { setInterval(ilio.pingUrls, ilio.pingInterval * 1000); }
      },
      makeLi: (url,i) => {
          var li = document.createElement("li");
          li.classList.add('status-non');
          li.innerHTML = '<span>?</span>'+(i ? '<span>'+i+')</span>':'')+'<a href="'+url+'" target="_blank">'+url.replace('https://easyforms.illinoislegalaid.org/start/','')+'</a>';
          li.setAttribute('title', url);
          return li;
      },      
      returnStatus: (req, elem) => {
        var status = ilio.serverSide ? ((req.status == 200 && req.responseText.length == 3) ? req.responseText : 'bad') : req.status;
        elem.getElementsByTagName('span')[0].innerHTML = status;
        elem.getElementsByTagName('span')[0].setAttribute('title', req.status);
        elem.removeAttribute('class');
        elem.classList.add('status-def', 'status-'+ status);
        ilio.maxActive++;
      },
      checkUrl: async (url, elem) => {
        while(ilio.maxActive <= 0) { await ilio.sleep(ilio.pingDelay); }
        ilio.maxActive--;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() { if(this.readyState == 4) { ilio.returnStatus(this, elem); }}
        if(ilio.serverSide) { xhr.open('GET', '?url='+url, true); } else { xhr.open('HEAD', url, true); }
        xhr.send();
      },
      pingUrls: () => {
        Array.from(document.getElementById("status-list").children).forEach(async (elem, i) => { await ilio.sleep(ilio.pingDelay * i); ilio.checkUrl(elem.title, elem); });
      },
      sleep: (ms) => { return new Promise(r => setTimeout(r, ms)); }
    }

    window.addEventListener('load', ilio.init);
    </script>
  </head>
  <body style="margin: 0;">
    <div style="background-color: #181c36; height; position: relative; top: 0; right: 0; left: 0; z-index: 1030; color: #fff; font-size: 1.5rem; padding: 1rem 4rem; margin: 0 0 0.25rem 0;">
      <svg width="73" height="30" style="float: left;" viewBox="0 0 73 30" fill="none" xmlns="http://www.w3.org/2000/svg"><g opacity="0.801223"><path fill-rule="evenodd" clip-rule="evenodd" d="M72.942 15.0535C72.942 6.79884 66.2314 0.107071 57.9535 0.107071C52.3797 0.107071 47.5265 3.14842 44.9442 7.65031L48.8681 15.3468C48.865 15.2488 48.8532 15.1525 48.8532 15.0535C48.8532 10.0743 52.9277 6.03743 57.9535 6.03743C62.9795 6.03743 67.054 10.0743 67.054 15.0535C67.054 20.0328 62.9795 24.0697 57.9535 24.0697C55.8273 24.0697 53.8762 23.3415 52.3271 22.1311L56.2897 29.9026C56.8364 29.963 57.3906 30 57.9535 30C66.2314 30 72.942 23.3082 72.942 15.0535" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0 29.9999H5.38264V0.0431213H0V29.9999Z" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M37.8847 0H36.125L23.8419 24.0511H15.6277V0.0428663H10.2389V29.9996H20.8041V29.9996H27.5419L36.8546 11.7688L46.2115 29.9996H53.1157L37.8847 0Z" fill="white"/></g></svg>
      <span style="margin-left: 1rem;">Docassemble Easy Form Status</span>
    </div>
    <div style="margin: 0.25rem;">
      <ul id="status-list" class="http-status"></ul>
    </div>
  </body>
</html>
<?php } ?>