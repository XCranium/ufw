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
    
    public function help() {
        echo "ufw - Helper to uFw framework\nOptions:\n";
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
    
}
