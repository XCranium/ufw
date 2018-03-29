<?php
namespace xbrain\ufw;

class AgentCli {
    
    
    const HELP = [
        "options" => [
            "--help" => [
                "summary" => "Shows this help."
            ],
            "--create" => [
                "summary" => "Create a new application.",
                "examples" => [
                    "--create=demo  \t create the application 'demo'"
                ]
            ], 
            "--init" => [
                "summary" => "Initialize the uFw workspace, creating folders and files needed."
                
            ], 
            "--run" => [
                "summary" => "Runs the application service defined by URI passwd by parameter.",
                "examples" => [
                    "--run=get:/app/demo/application_route",
                    "--run=post:/app/demo/user",
                    "--run=put:/app/demo/user/123"
                ]
                
            ],
            "--data" => [
                "summary" => "Pass data to application (Presumes the --run option)",
                "format" => "String in JSON notation",
                "description" => "Used",
                "examples" => [
                    "--data='{\"email\":\"email@domain.com\"}'",
                    "--data='[{\"record\":625, \"code\":\"AB123\"},{\"record\":731, \"code\":\"23F4E\"}]'"
                ]
            ]
        ]
    ];
    
    
    const DEFAULT_CONFIG = [
            'delay' => 5,
            'host' => 'http://localhost:8008',
            'config_ttl' => 90,       
            'lock_dir' => './',
            'db_tasks' => [
                'source_type' => 'config',
                'tasks_ttl' => 60
            ]
        ];
    
    
    protected $run;
    protected $config;
    protected $tasks = [];
    
    
    public function help() {
        echo "ufw - Async agent to uFw framework\nOptions:\n";
        foreach (self::HELP['options'] as $helpOption => $helpDescription) {
            echo "\n   $helpOption \t " . ($helpDescription['summary']??"");
            if (array_key_exists('examples', $helpDescription)) {
                echo "\n            \t Ex:";
                foreach ($helpDescription['examples'] as $example) {
                    echo "\n           \t   $example";
                }
                echo "\n";
            }
        }
        echo "\n";
    }
    
    
    public function run() {
        
        if (Console::getArg('--config')) {
            $this->applyConfig('cli',Console::getArg('--config'));
            //return;
        } elseif (Console::getArg('--config-file')) {
            $this->applyConfig('file',Console::getArg('--config-file'));
        }
        
        if (Console::getArg('--tasks')) {
            $this->applyTasks('cli',Console::getArg('--tasks'));
            //return;
        } elseif (Console::getArg('--tasks-file')) {
            $this->applyTasks('file',Console::getArg('--tasks-file'));
        }
        
        
        
        
        if (Console::getArg('--run')) {
            $this->runAgent();
            return;
        } elseif (Console::getArg('--status')) {
            $this->checkStatus();
            return;
        }
        
        
//        if (Console::getArg('--create')) {
//            $this->createProject(Console::getArg('--create'));
//            return;
//        } elseif (Console::getArg('--init')) {
//            $this->initWorkspace();
//            return;
//        } elseif (Console::getArg('--run')) {
//            $this->runAction(Console::getArg('--run'),json_decode(Console::getArg('--data','[]'),true)  );
//        }
        
        
        
        $this->help();
    }
    
    
    public function checkStatus() {
        
        try {
            $status = $this->readLockFile();
            $ph = new utils\ProcessHandler(true);
            
            echo "\n";
            foreach ($status as $pid => $data) {
                
                if ($ph->getProcess($pid)) {
                    $stat = ' running ';
                } else {
                    $stat = ' missing ';
                }
                
                echo "\n pid:            $pid       ($stat)";
                echo "\n started:       " . date('Y-m-d H:i:s',$data['started']);
                echo "\n configuration: " . json_encode($data['config'])."\n---\n";
            }
            
            
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
        
    }
    
    
    
    public function __construct() {
        $this->applyConfig('cli', '[]');
    }
    
    
    protected function runAgent() {
        
        $this->run = true;
        
        $this->readConfig();
        
        
        if (empty($this->tasks)) {
            echo "\n\n##################################\n## Error: no tasks assigned \n##################################\n\n";
            exit;
        }
        
        $this->writeLockFile();
        
        while($this->run) {
            
            $task = $this->getNextTask();
            
            if ($task) {
                $this->runTask($task);
            }
            echo "\n " . date('His');
            $this->sleep();
            echo "\n " . date('His');
            $this->readConfig();
            
            //print_r(['config'=>$this->config,'tasks'=>$this->tasks]);
            
        }
        
        
        
    }
    
    
    protected function getLockDir() {
        $lockDir = Utils::get('lock_dir', $this->config,'./');
        return $lockDir;
    }
    
    protected function getLockFile() {
        $lockFile = $this->getLockDir()."/ufw.lock";
        return $lockFile;
    }
    
    protected function writeLockFile() {
        
        $lockDir = $this->getLockDir();
        if (!is_writable($lockDir)) {
            throw new \Exception("Folder not writable ($lockDir)",7);
        }
        
        $lockFile = $this->getLockFile();
        
        if (file_exists($lockFile)) {
            $lock = json_decode(file_get_contents($lockFile),true);
        }
        
        $lock[getmypid()] = [
            'started' => time(),
            'pid' => getmypid(),
            'config' => $this->config
        ];
        
        file_put_contents($lockFile, json_encode($lock));
    }
    
    
    protected function readLockFile() {
        
        $lockDir = $this->getLockDir();
        if (!is_writable($lockDir)) {
            throw new \Exception("Folder not writable ($lockDir)",7);
        }
        
        $lockFile = $this->getLockFile();
        
        if (file_exists($lockFile)) {
            $lock = json_decode(file_get_contents($lockFile),true);
        } else {
            throw new \Exception("Lock file does not exists",8);
        }
        return $lock;
        
    }
    
    
    protected function applyConfig($type, $data) {
        if ($type == 'cli') {
            
            $json = json_decode($data,true);
            $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $json);
            
        } elseif ($type == 'file') {
            if (file_exists($data)) {
                $text = file_get_contents($data);
                $json = json_decode($text,true);
                $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $json);
            }
        }
    }
    
    
    protected $executionPlan = [];
    protected $nextExecution = 0;
    
    protected function applyTasks($type, $data) {
        if ($type == 'cli') {
            
            $json = json_decode($data,true);
            $this->tasks = $json;
            
        } elseif ($type == 'file') {
            if (file_exists($data)) {
                $text = file_get_contents($data);
                $json = json_decode($text,true);
                $this->tasks = array_replace_recursive($this->tasks, $json);
            }
        }
        
        foreach ($this->tasks as $k => $task) {
            $this->executionPlan[] = $k;
        }
        $this->nextExecution = 0;
        
    }
    
    
    public function sleep() {
        $n = Utils::get('delay', $this->config, 10);
        sleep($n);
    }
    
    public function getNextTask() {
        $i =  $this->executionPlan[$this->nextExecution++];        
        $this->nextExecution = $this->nextExecution % count($this->executionPlan);
        return $this->tasks[$i];
    }
    
    public function runTask($task) {
        
        
        $baseURI = Utils::get('base_uri', $task, Utils::get('host', $this->config, 'http://localhost/'));
        
        
        $client = new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $baseURI
        ]);
        
        
        
        $httpMethod = Utils::get('method', $task, 'GET');
        $uri = Utils::get('uri', $task, '/');
        $options = Utils::get('options', $task, []);
        
        
        $response = $client->request($httpMethod, $uri, $options);
        
        $body = $response->getBody();
        
        if (Utils::get('output', $task)) {
            file_put_contents(Utils::get('output', $task), $body);
        }
        
    }
    
    
    public function readConfig() {
        

        
        //$this->config = [];
        
        
    }
    
    
    
    
    public function checkAndRestart() {
        
    }
    
    
    
    
}
