<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload902a9a736e13b729569b0c6f0dd931c2($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'projectquotahtml' => '/ProjectQuotaHtml.class.php',
            'projectquotamanager' => '/ProjectQuotaManager.class.php',
            'projectsoverquotatablepresenter' => '/ProjectsOverQuotaTablePresenter.class.php',
            'projectsoverquotatableheaderpresenter' => '/ProjectsOverQuotaTableHeaderPresenter.class.php',
            'statistics_diskusagedao' => '/Statistics_DiskUsageDao.class.php',
            'statistics_diskusagegraph' => '/Statistics_DiskUsageGraph.class.php',
            'statistics_diskusagehtml' => '/Statistics_DiskUsageHtml.class.php',
            'statistics_diskusagemanager' => '/Statistics_DiskUsageManager.class.php',
            'statistics_diskusageoutput' => '/Statistics_DiskUsageOutput.class.php',
            'statistics_formatter' => '/Statistics_Formatter.class.php',
            'statistics_formatter_cvs' => '/Statistics_Formatter_Cvs.class.php',
            'statistics_formatter_scm' => '/Statistics_Formatter_Scm.class.php',
            'statistics_formatter_svn' => '/Statistics_Formatter_Svn.class.php',
            'statistics_projectquotadao' => '/Statistics_ProjectQuotaDao.class.php',
            'statistics_scmcvsdao' => '/Statistics_ScmCvsDao.class.php',
            'statistics_scmsvndao' => '/Statistics_ScmSvnDao.class.php',
            'statistics_services_usageformatter' => '/Statistics_Services_UsageFormatter.class.php',
            'statistics_servicesusagedao' => '/Statistics_ServicesUsageDao.class.php',
            'statistics_soapserver' => '/Statistics_SOAPServer.class.php',
            'statistics_widget_projectstatistics' => '/Statistics_Widget_ProjectStatistics.class.php',
            'statisticsplugin' => '/statisticsPlugin.class.php',
            'statisticsplugindescriptor' => '/StatisticsPluginDescriptor.class.php',
            'statisticsplugininfo' => '/StatisticsPluginInfo.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload902a9a736e13b729569b0c6f0dd931c2');
// @codeCoverageIgnoreEnd
