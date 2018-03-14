<?php

namespace xbrain\ufw;

class Setup {
    
    const FILES_BASE = [
        'bootstrap/init.php' => 'PD9waHANCnJlcXVpcmUgX19ESVJfXy4iLy4uL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KcmVxdWlyZSBfX0RJUl9fLiIvLi4vLi4vdWZ3L3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KDQp1c2UgeGJyYWluXHVmd1xBcHBsaWNhdGlvbjsNCg0KJHJlcXVlc3QgPSBceGJyYWluXHVmd1xSZXF1ZXN0OjpvZigpOw0KDQoNCiRhcHAgPSBBcHBsaWNhdGlvbjo6Z2V0SW5zdGFuY2UoWw0KICAgIEFwcGxpY2F0aW9uOjpERUZfQVBQU19QQVRIID0+ICcuLi9hcHBzLycsDQogICAgQXBwbGljYXRpb246OkNNUF9ST1VURVIgPT4gbmV3IHhicmFpblx1ZndcUm91dGVyKCksDQogICAgQXBwbGljYXRpb246OkNNUF9SRVFVRVNUID0+ICRyZXF1ZXN0LA0KICAgIEFwcGxpY2F0aW9uOjpDTVBfQ09ORklHID0+IFx4YnJhaW5cdWZ3XENvbmZpZzo6b2YoX19ESVJfXykNCl0pOw0KDQokYXBwLT5pbml0KCk7DQoNCiRhcHAtPnJ1bigpOw0KDQo=',
        'config/routes.json' => 'ew0KICAgICJyb3V0ZXMiIDogew0KICAgICAgICAiL2luZm8iIDogew0KICAgICAgICAgICAgIkdFVCIgOiB7DQogICAgICAgICAgICAgICAgImV2YWwiIDogInBocGluZm8oKTsiDQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQp9',
        'public/.htaccess' => 'PElmTW9kdWxlIG1vZF9yZXdyaXRlLmM+IA0KICAgIDxJZk1vZHVsZSBtb2RfbmVnb3RpYXRpb24uYz4gICAgICAgIA0KICAgICAgIE9wdGlvbnMgLU11bHRpVmlld3MgIA0KICAgIDwvSWZNb2R1bGU+IA0KDQogICAgUmV3cml0ZUVuZ2luZSBPbiANCiAgICBSZXdyaXRlQ29uZCAle1JFUVVFU1RfRklMRU5BTUV9ICEtZCANCiAgICBSZXdyaXRlQ29uZCAle1JFUVVFU1RfRklMRU5BTUV9ICEtZiANCiAgICBSZXdyaXRlUnVsZSBeIGluZGV4LnBocCBbTF0gDQogIDwvSWZNb2R1bGU+',
        'public/index.php' => 'PD9waHANCg0KdHJ5eyANCiAgICAgcmVxdWlyZV9vbmNlICcuLi9ib290c3RyYXAvaW5pdC5waHAnOyANCiB9IGNhdGNoIChcRXhjZXB0aW9uICRleCkgew0KICAgICRkYXRhID0gWydleGNlcHRpb24nID0+IHRydWUsICdzdWNjZXNzJyA9PiBmYWxzZSwgJ21zZycgPT4gJGV4LT5nZXRNZXNzYWdlKCksJ2NvZGUnPT4kZXgtPmdldENvZGUoKV07ICANCiAgICANCiAgICANCiAgICBoZWFkZXIoJ0NvbnRlbnQtdHlwZTogdGV4dC9qc29uJyk7DQogICAgZWNobyBqc29uX2VuY29kZSgkZGF0YSwgSlNPTl9PQkpFQ1RfQVNfQVJSQVkpIC4gIlxuIjsNCiAgICBleGl0OyANCiB9IA=='
    ];
    
    const FILES_APP = [
        'bootstrap.php' => 'PD9waHANCiRpbmNsdWRlID0gX19ESVJfXyAuIi92ZW5kb3IvYXV0b2xvYWQucGhwIjsNCmluY2x1ZGUgJGluY2x1ZGU7',
        'routes/main.json' => 'ew0KICAgICJyb3V0ZXMiOiB7DQogICAgICAgICIvIiA6IHsNCiAgICAgICAgICAgICJHRVQiIDogew0KICAgICAgICAgICAgICAgICJldmFsIiA6ICJwaHBpbmZvKCk7Ig0KICAgICAgICAgICAgfQ0KICAgICAgICB9DQogICAgfQ0KfQ=='
    ];
    
    
    public function help() {
        echo "ufw \n Usage...";
    }
    
    
    public function run() {
        
        
        if (Console::getArg('--create')) {
            $this->createProject(Console::getArg('--create'));
            return;
        } elseif (Console::getArg('--init')) {
            $this->initWorkspace();
            return;
        }
        
        
        
        $this->help();
    }
    
    
    protected function initWorkspace() {        
        $this->createDirectory(['apps', 'bootstrap', 'config', 'public']);
        foreach (self::FILES_BASE as $pathname => $contents) {
            $this->createFile($pathname, $contents);
        }
    }
    
    
    protected function createProject($projectName='myProj') {        
        Console::stdout("Creating project $projectName... ");        
        
        $this->createDirectory("./apps/".$projectName, ['src', 'routes', 'bootstrap', 'config', 'public', 'doc','templates']);
        echo "\n";
        foreach (self::FILES_APP as $pathname => $contents) {
            $this->createFile("./apps/".$projectName."/$pathname", $contents);
        }        
        echo "\nDone!\n";
    }
    
    
    protected function createDirectory($path, $children=[]) {
        $success =  true;
        if (is_array($path)) {
            foreach ($path as $item) {
                $this->createDirectory($item);
            }
        } elseif (is_scalar($path)) {
            
            if (strpos($path, '/') !== false) {
                $pathParts = explode("/", $path);
                $path = '';
                foreach ($pathParts as $item) {
                    if (!empty($item)) {
                        $path .= $item;
                       if (!file_exists($path)) {
                           echo "\n Creating $path ";
                            mkdir($path);
                       }
                       $path .= '/';
                    }
                }
            } else {
                if (file_exists($path)) {
                    $success = true;
                } else {
                    echo "\n Creating $path ";
                    $success = mkdir($path);
                }
            }
            
            if (!$success) {
                throw new \Exception("Error creating $path");
            }
            
            if ($children) {
                foreach ($children as $item) {
                    $this->createDirectory($path.'/'.$item);
                }
            }
            
        }
        
        
    }
    
    
    protected function createFile($pathname, $contents) {
        
        if (strpos($pathname, '/') !== false) {
            $dirName = dirname($pathname);
            $this->createDirectory($dirName);            
        }
        if (!file_exists($pathname)) {
            echo "\n Creating file $pathname ";
            file_put_contents($pathname, base64_decode($contents));
        }
        
    }
    
    
}