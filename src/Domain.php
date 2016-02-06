<?php

namespace Gecche\Multidomain;

use Input;
use App;
use Illuminate\Filesystem\Filesystem;

class Domain implements DomainInterface {

    protected $environment;
    protected $domain;
	 protected $domain_sanitized;
    protected $files;

    public function __construct(Filesystem $files) {
        $this->files = $files;
    }

    public function getEnvironment() {
        return $this->environment;
    }

    public function setEnvironment($environment = null) {
        if ($environment === null || !is_string($environment)) {
            $environment = App::environment();
        }
        $this->environment = $environment;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function setDomain($domain = null) {
        if ($domain === null || !is_string($domain)) {
            $domain = App::detectDomain();
        }
        $this->domain = $domain;
		  $this->domain_sanitized = domain_sanitized($domain);
    }
    
    public function getDomainSanitized() {
        return $this->domain_sanitized;
    }

    
    /*
     * 
     * Metodi per determinare i vari path
     * 
     */

    protected function getPath() {
        return app_path() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "environments.php";
    }

    protected function getEnvFilePath($environment = null) {
        if ($environment == null) {
            $environment = $this->getEnvironment();
        }
        
        if ($environment == 'production') {
            return base_path() . DIRECTORY_SEPARATOR . ".env.php";
        }
        return base_path() . DIRECTORY_SEPARATOR . ".env.{$environment}.php";
    }
    
    protected function getEnvConfigPath($environment = null) {
        if ($environment == null) {
            $environment = $this->getEnvironment();
        }
        
        if ($environment == 'production') {
            return app_path() . DIRECTORY_SEPARATOR . "config";
        }
        return app_path() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . $environment;
    }
    
    protected function getEnvConfigDomainPath($domain = null,$environment = null) {
        if ($domain == null) {
            $domain = $this->getDomainSanitized();
        }
        return $this->getEnvConfigPath($environment) . DIRECTORY_SEPARATOR . "domain" . DIRECTORY_SEPARATOR . $domain;
    }

    
    protected function getEnvVarsFilePath($environment = null) {
        return $this->getEnvConfigPath($environment) . DIRECTORY_SEPARATOR . "environment.php";
    }
    
    protected function getEnvDomainVarsFilePath($environment = null) {
        return $this->getEnvConfigPath($environment) . DIRECTORY_SEPARATOR . "domain.php";
    }
    
    protected function getDomainVarsFilePath($domain = null,$environment = null) {
        return $this->getEnvConfigDomainPath($domain,$environment) . DIRECTORY_SEPARATOR . "domain.php";        
    }
    
    protected function getStorageDirsPath($domain = null) {
        if ($domain == null) {
            $domain = $this->getDomain();
        }         
        return storage_path() . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $domain;
    }
    
    
    /*
     * Recupera ed imposta il file environment.php in app/config dove sono listati i vari ambienti e domini
     * 
     * 
     * 
     * 
     */
    public function getConfig() {
        $environments = require $this->getPath();

        if (!is_array($environments)) {
            $environments = [];
        }

        return $environments;
    }

    public function setConfig($config) {
        $code = "<?php\n\nreturn [";

        foreach ($config as $environment => $hosts) {
            $code .= $this->formattedArrayLineOpen($environment, 1);

            foreach ($hosts as $host) {
                $code .= $this->formattedArrayLine($host, null, 2);
            }

            $code .= $this->formattedArrayLineClose(1);
        }

        $code = trim($code, ",");
        $this->files->put($this->getPath(), $code . "\n];");
    }

    /*
     * Crea le varie cartelle i {environment} se non le trova
     * 
     * /app/config/{enivronment}
     * /app/config/{environment}/domain
     * 
     * 
     */

    public function createEnvironment($envFile = [],$domainFile = [],$domain = null,$environment = null) {
        $env_config_path = $this->getEnvConfigPath($environment);
        $env_config_domain_path = $this->getEnvConfigDomainPath($domain,$environment);

        $env_domain_vars_file = $this->getEnvDomainVarsFilePath($environment);
        $env_vars_file = $this->getEnvVarsFilePath($environment);
        $productionenv_domain_vars_file = $this->getEnvDomainVarsFilePath("production");
        $productionenv_vars_file = $this->getEnvVarsFilePath("production");
        
        $localenv_config_path = $this->getEnvConfigPath("local");

        //Creo il file domain.php nella config root se non esiste
        if (!$this->files->exists($productionenv_domain_vars_file)) {
            $this->setEnvDomainVarsFile($domainFile, 'production');
        }
        //Creo il file environment.php nella config root se non esiste
        if (!$this->files->exists($productionenv_vars_file)) {
            $this->setEnvVarsFile($envFile, 'production');
        }
               
        if ($this->environment == 'production') {
            if (!$this->files->exists($env_config_domain_path)) {
                $this->files->makeDirectory($env_config_domain_path,0755,true);
            }
            return;
        }
        
        if ($this->environment == 'local') {
            if (!$this->files->exists($env_config_path)) {       
                $this->files->makeDirectory($env_config_path);
            }
            if (!$this->files->exists($env_domain_vars_file)) {
                $this->setEnvDomainVarsFile($domainFile);
            }
            if (!$this->files->exists($env_vars_file)) {
                $this->setEnvVarsFile($envFile);
            }
            if (!$this->files->exists($env_config_domain_path)) {
                $this->files->makeDirectory($env_config_domain_path,0755,true);
            }
            return;
        }
        
        if (!$this->files->exists($env_config_path)) {       
            $this->files->makeDirectory($env_config_path);
            if (!$this->files->exists($localenv_config_path)) {       
                $this->files->makeDirectory($localenv_config_path);
            }
            
            $localFiles = $this->files->files($localenv_config_path);
            foreach ($localFiles as $file) {
                $filename = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);
                $this->files->copy($localenv_config_path . DIRECTORY_SEPARATOR . $filename, $env_config_path . DIRECTORY_SEPARATOR . $filename);
            }
        }
        if (!$this->files->exists($env_domain_vars_file)) {
            $this->setEnvDomainVarsFile($domainFile);
        }
        if (!$this->files->exists($env_vars_file)) {
            $this->setEnvVarsFile($envFile);
        }
        if (!$this->files->exists($env_config_domain_path)) {       
            $this->files->makeDirectory($env_config_domain_path,0755,true);
        }
        
    }
    
    /*
     * Recupera ed imposta il file .env.{environment}.php nella root dove sono scritte le variabili segrete
     * 
     * 
     * 
     * 
     */
    
    public function getEnvFile($environment = null) {
        
        if (!file_exists($this->getEnvFilePath($environment))) {
            $code = "<?php\n\nreturn [];";
            $this->files->put($this->getEnvFilePath($environment), $code);
        }

        $environment_file = require $this->getEnvFilePath($environment);

        return $environment_file;
    }

    public function setEnvFile($envFile, $environment = null) {

        $this->setVarsFile($envFile, $this->getEnvFilePath($environment));
        return;
    }
    
     /*
     * Recupera ed imposta i files domain.php e environment.php nella config di ambiente e/o dominio
     * 
     * 
     * 
     * 
     */
    public function getEnvVarsFile($environment = null) {
        
        $env_vars_file_path = $this->getEnvVarsFilePath($environment);
        
        return $this->getVarsFile($env_vars_file_path);
    }

    public function setEnvVarsFile($envFile, $environment = null) {
        
        $this->setVarsFile($envFile, $this->getEnvVarsFilePath($environment));
    }
    
    public function getEnvDomainVarsFile($environment = null) {
        
        $env_domain_vars_file_path = $this->getEnvDomainVarsFilePath($environment);
        
        return $this->getVarsFile($env_domain_vars_file_path);
    }

    public function setEnvDomainVarsFile($domainFile, $environment = null) {
        
        $this->setVarsFile($domainFile, $this->getEnvDomainVarsFilePath($environment));
    }

    public function getDomainVarsFile($domain = null,$environment = null) {
        
        $domain_vars_file_path = $this->getDomainVarsFilePath($domain,$environment);
        
        return $this->getVarsFile($domain_vars_file_path);
    }

    public function setDomainVarsFile($domainFile, $domain = null,$environment = null) {
        
        $this->setVarsFile($domainFile, $this->getDomainVarsFilePath($domain,$environment));
    }

    
  
    protected function getVarsFile($filePath) {
        
        if (!file_exists($filePath)) {
            $code = "<?php\n\nreturn [];";
            $this->files->put($filePath, $code);
        }

        $file = require $filePath;

        return $file;
    }

    protected function setVarsFile($file, $filePath) {                
        $code = "<?php\n\nreturn " . var_export($file,true) . ';';        
        $this->files->put($filePath, $code);
    }    

    
    /*
     * 
     * Elimina le direcotries del dominio
     * 
     */
    
    public function deleteDomainFiles($domain = null) {
        $path = $this->getEnvConfigDomainPath($domain);
        $this->files->deleteDirectory($path);
    }
    
     /*
     * Crea e rimuove le storage dirs del dominio
     * 
     * 
     * 
     * 
     */
    
    public function createDomainStorageDirs($storageDirs,$domain = null) {
        $path = $this->getStorageDirsPath($domain);
        
        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path,0755,true);
        }
        
        foreach ($storageDirs as $dir) {
            $storageDir = $path . DIRECTORY_SEPARATOR . $dir;
            if (!$this->files->exists($storageDir)) {
                $this->files->makeDirectory($storageDir,0755,true);
            }
        }
        
    }
    
    /*
     * 
     * Elimina le direcotries del dominio
     * 
     */
    
    public function deleteDomainStorageDirs($domain = null) {
        $path = $this->getStorageDirsPath($domain);
        $this->files->deleteDirectory($path);
    }
    
    /*
     * 
     * Helpers per la scrittura di array json su files
     * Alla fine uso var_export per sicurezza
     * 
     */
    protected function formattedArrayLine($key, $value = null, $level = 1) {
        if ($value === null) {
            return "\n" . str_pad("", $level * 3, " ", STR_PAD_RIGHT) . '"' . $key . '",';
        }
        return "\n" . str_pad("", $level * 3, " ", STR_PAD_RIGHT) . '"' . $key . '" => "' . $value . '",';
    }

    protected function formattedArrayLineOpen($key, $level = 1) {
        return "\n" . str_pad("", $level * 3, " ", STR_PAD_RIGHT) . '"' . $key . '" => [';
    }

    protected function formattedArrayLineClose($level = 1) {
        return "\n" . str_pad("", $level * 3, " ", STR_PAD_RIGHT) . '],';
    }

}
