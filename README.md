# alive-ho
A simple echo server written in Rust for testing whether a computer is running, with useful example PHP code of how to call

1. [About](#about)
2. [Installing alive-ho](#installing-alive-ho)
3. [alive-hi](#alive-hi)
4. [Installing alive-hi](#installing-alive-hi)

## About
I have lots of computers. Some are x86, most are ARM64 Raspberry Pi (some are ARM32). I wanted to have a page on my main intranet server that displayed their up/down status.

I copied some Rust code I found for a TCP echo server, stripped out the `println!` macros because this was going to run as a service, and corrected an horrendous memory leak that also sent the CPU to 100% utilisation on a core (one core per call). People shouldn't post untested code!

## Installing alive-ho
First, edit a service file:
```
sudo nano /etc/systemd/system/alive-ho.service
```
Enter the following details:
```
[Unit]
Description=Alive-ho network alive messaging
After=network.target
StartLimitIntervalSec=0

[Service]
Type=simple
Restart=always
RestartSec=1
User=username
ExecStart=/home/username/bin/alive-ho

[Install]
WantedBy=multi-user.target
```
In the `[Service]` block, change the `User` and `ExecStart` name and folders to something useful.

Next, start the service and test:
```
sudo systemctl start alive-ho
```

If all goes well, enable to start at boot and set a watchdog on it:
```
sudo systemctl enable alive-ho
```

## alive-hi
This is the PHP code that calls the server(s). Why PHP? Because you probably want to display your results in an entirely different way from me, and the code is a doddle to play around with.

The PHP code picks up a list of servers from a json file with the following basic format:
```
{
    "defport": "48947",
    "servers": [
        {
            "server": "NAS",
              "port": "44443"
        },
        {
            "server": "192.168.1.2"
        }
    ]
}

```
As you can see, each server can have its own port setting, but you can set the `defport` (default port) and if an individual server section doesn't have a port defined, it will assume the `defport` value.

Note you can use either a hostname or an ip address (ipv4 or ipv6 are both valid), but a hostname is more readable, especially when you have more than two machines.

Note also that servernames, ip addresses and port numbers are expressed as strings.

## Installing alive-hi
Just drop `alive-hi.php` and `alive-hi.json` somewhere on your intranet's web space - just ensure they are both in the same web folder.

My webfolder is on a Raspberry Pi running Ubuntu Server, and with 13 servers to test (4 without alive-ho running to test downtime responses), it takes less than 1 second to open the new tab and display the results. In the meantime, conky shows alive-ho not consuming resources when there are no requests to service (in fact, conky is using more resources *sigh*).
