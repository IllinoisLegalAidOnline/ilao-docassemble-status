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
    <title>ILIO Status Monitor</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Status Monitor for ILIO.">
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
      .http-status li {
        padding: 5px;
        width: 100px;
        height: 100px;
        margin: 10px;
        line-height: 100px;
        color: white;
        font-weight: bold;
        font-size: 2em;
        text-align: center;
      }
      .status-non { background: #aaaaaa; }  
      .status-def { background: #aa0000; }  
      .status-200 { background: #00aa00; }  
      .status-308 { background: #0000aa; }  
      .status-bad { background: #666666; }  
    </style>

    <script>
    ilio = {
      serverSide: true,
      pingInterval: 6, // Seconds
      urls: [
        'https://easyforms.illinoislegalaid.org/run/Appearance/appearance',
        'https://easyforms.illinoislegalaid.org/run/AppearanceEfile/appearance',
        'https://easyforms.illinoislegalaid.org/run/AskDebtCollectorStopContact/stop_debt_collection',
        'https://easyforms.illinoislegalaid.org/run/CollectionProofLetter/collection_proof_debtor',
        'https://easyforms.illinoislegalaid.org/run/EFilingExemptionAppellateCourt/e-filing_exemption_appellate_court',
        'https://easyforms.illinoislegalaid.org/run/EfilingExemptionCircuitCourt/e-filing_exemption',
        'https://easyforms.illinoislegalaid.org/run/EFilingExemptionSupremeCourt/e-filing_exemption_supreme_court',
        'https://easyforms.illinoislegalaid.org/run/EndLockout/end_lockout_letter',
        'https://easyforms.illinoislegalaid.org/run/EvictATenant/evict_a_tenant',
        'https://easyforms.illinoislegalaid.org/run/FeeWaiver/fee_waiver',
        'https://easyforms.illinoislegalaid.org/run/IDHRHousingDiscriminationComplaint/housing_discrimination_complaint',
        'https://easyforms.illinoislegalaid.org/run/LivingWill/living_will',
        'https://easyforms.illinoislegalaid.org/run/RequestTimeOffWorkDueToDomesticViolence/request_time_off_work_due_to_domestic_violence',
        'https://easyforms.illinoislegalaid.org/run/RespondToALawsuit/respond_to_a_lawsuit',
        'https://easyforms.illinoislegalaid.org/run/SecurityDepositDemand/security_deposit_demand_letter',
        'https://easyforms.illinoislegalaid.org/run/StopWageAssignment/stop_wage_assignment',
        'https://easyforms.illinoislegalaid.org/run/WhichProtectiveOrderIsRightForMe/which_protective_order',
      ],

      returnStatus: (req, elem) => {
        var status = ilio.serverSide ? (req.status == 200 ? req.responseText : 'bad') : req.status;
        elem.innerHTML = status;
        elem.removeAttribute('class');
        elem.classList.add('status-def', 'status-'+ status);
      },
      checkUrl: (url, elem) => {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() { if(this.readyState == 4) ilio.returnStatus(this, elem); }
        if(ilio.serverSide) {
          xhr.open('GET', '?url='+url, true);
        }
        else {
          xhr.open('HEAD', url, true);
        }
        xhr.send();
      },
      pingUrls: () => {
        var elems = document.getElementById("status-list").children;
        ilio.urls.forEach((url, index) => { ilio.checkUrl(url, elems[index]); });
      }
    }

    window.addEventListener('load', function () {
      var ul = document.getElementById("status-list");
      ilio.urls.forEach(url => {
        var li = document.createElement("li");
        li.classList.add('status-non');
        li.innerHTML = '?';
        li.setAttribute('title', url);
        ul.appendChild(li);
      });
      setInterval(ilio.pingUrls, ilio.pingInterval * 1000);
    });

    </script>
  </head>
  <body>
    <div>
      <ul id="status-list" class="http-status"></ul>
    </div>
  </body>
</html>
<?php } ?>