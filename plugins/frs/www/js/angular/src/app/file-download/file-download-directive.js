import "./file-download.tpl.html";

import FileDownloadController from "./file-download-controller.js";

export default fileDownloadDirective;

function fileDownloadDirective() {
    return {
        restrict: "A",
        scope: {
            file: "=fileDownload",
            license_approval_mandatory: "=licenseApprovalMandatory"
        },
        templateUrl: "file-download.tpl.html",
        controller: FileDownloadController,
        controllerAs: "$ctrl",
        bindToController: true
    };
}
