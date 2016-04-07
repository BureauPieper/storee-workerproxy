Light-weight worker proxy for work queues
=========================================

Long running tasks directly on a underlying (PHP) system can be quite daunting and challenging to say the least, one often has to deal with
way too many circumstances in subsystems that aren't really expected to be long-running.

This simple proxy can be setup through supervisord for example to watch the beanstalkd tubes in order to pass the payload to an endpoint in your main system.

We use this in Store-E for over 200 concurrent processes.
 
### Usage

```
php /pathtothisworker/app/console.php worker:watch-tube tube_to_watch 'php /pathtomysystem/console some:command:run ... %%payload%%'
```

We use base64 encoded JSON data for payloads, keep in mind that you'll need additional escaping without encoding the data. Feel free to use
our fork for base64 decoding support in beanstalk_console.

https://github.com/BureauPieper/beanstalk_console

As of now output and calls is written to app/logs. It's recommended to setup real log channels in your main system.

#### Supervisord example
```
[program:some_command]
command=php /pathtothisworker/app/console.php worker:watch-tube tube_to_watch 'php /pathtomysystem/console some:command:run %%payload%%'
autostart=true
autorestart=true
stderr_logfile=/pathtothisworker/app/logs/%(program_name)s.err.log
stdout_logfile=/pathtothisworker/app/logs/%(program_name)s.log
process_name=%(program_name)s_%(process_num)02d
numprocs=20
stdout_logfile_maxbytes=1MB
stderr_logfile_maxbytes=1MB
```

