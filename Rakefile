task :package do
  `zip -r sugarcrm_connector_for_ibm_connections_v2_social.zip icons/ scripts/ SugarModules/ LICENSE.txt manifest.php`
end

task :sync do
  instance_dir = "/Applications/MAMP/htdocs/sugarcrm-6.5.11-ult"
  puts `rsync -av ./SugarModules/modules/ibm_connections #{instance_dir}/modules/ibm_connections`
  puts `rsync -av ./SugarModules/custom #{instance_dir}/custom`
end