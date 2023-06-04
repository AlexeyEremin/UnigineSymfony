# README #

This is a test work from Web Department of Unigine Company.

### How do I get set up? ###

* Install Docker Compose
* Install Git
* Clone this repository
* Put ```127.0.0.1 url-shortener.loc``` into your hosts file
* Run ```docker-compose up``` in the root of the repository
* Go to ```http://url-shortener.loc``` in your browser

### How do I use it? ###

* To encode ```someurl``` you can use ```/encode-url?url=someurl``` endpoint
* To decode ```somehash``` you can use ```/decode-url?hash=somehash``` endpoint


### New functionality ###
#### Tasks number
1. Go to redirect URL: site/gourl/{hash}
2. Added search conditions for previously created links, if not active, creating new
3. The lifetime of the link is set in a constant, now 1 day
4. Create command: 
```sh 
php bin/console command:get-information-url {url} {date-create} 
```
5. Create sub-tasks:
   1. Create encode URL and DateTime: /encode-url?url=URL/DateTime
   2. Create statistic: /statistic/url-hash/{hash}
   3. Create statistic between date: /statistic/url-unique-between/{startDate}/{endDate}
   4. Create unique url: /statistic/url-unique/{url}

Thanks, my first project on Symfony)