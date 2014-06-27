task :package do
  `find . -name ".DS_Store" -exec rm '{}' \\;`
  `zip -r sugarcrm_connector_for_ibm_connections_v2_social.zip icons/ scripts/ SugarModules/ LICENSE.txt manifest.php`
end

task :sync do
  instance_dir = "/Applications/MAMP/htdocs/sugarcrm-6.5.11-ult"
  puts `rsync -av ./SugarModules/modules/ibm_connections #{instance_dir}/modules/ibm_connections`
  puts `rsync -av ./SugarModules/custom #{instance_dir}/custom`
end

task :translate do
  source  = "en_us"
  targets = %w{es_ES}
  dirs    = ['./SugarModules/modules/ibm_connections/language', './SugarModules/language/application', './SugarModules/connectors/connections/language'] 
  #targets = %w{bg_BG ca_ES cs_CZ da_DK de_DE en_UK es_ES et_EE fr_FR he_IL hu_HU it_it ja_JP lt_LT nb_NO nl_NL pl_PL pt_PT ro_RO ru_RU sr_RS sv_SE tr_TR zh_CN}
  targets.each do |target|
    puts "Translating from #{source} to #{target}"
    dirs.each do |dir|
      s = File.read(path_to_language(dir, source))
      t = File.read(path_to_language(dir, target))
      diff_labels(s,t)
    end
  end
end

def path_to_language(dir, lang)
  dir + "/" + lang + ".lang.php"
end

def diff_labels(s,t)
  puts s
end
