# WeatherAPI
Symfony 4 Weather microservice

## Clone the repository 

```bash
git clone https://github.com/arconale/YahooWeatherAPI.git
```

## Install dependencies 
```bash
cd YahooWeatherAPI
composer install
```
## Configuration 

We need to add Yahoo's Rest API credentiels in order to grant access to thiere API.
for more details please see the link below 
https://developer.yahoo.com/weather/

add those two variables to .env file 

```text
CONSUMER_KEY=xxxxxxxxxxxxx
CONSUMER_SECRET=xxxxxxxxxxxxx
```
Change the CONSUMER_KEY value by the Client ID and the CONSUMER_SECRET value by Client Secret you get from the yahoo developer console 

you can use my credentiels for test

```text
CONSUMER_KEY=dj0yJmk9RHdjbFlDTmxkM29nJmQ9WVdrOWJUQnZRMmR2TjJrbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmc3Y9MCZ4PTAy
CONSUMER_SECRET=229808bf89acf958b91e32f8fe12b6915c3ce291
```

## Run developement server

```bash
php bin/console server:run
```

## Test

Open this url http://localhost:8000/graphiql in your browser and start making graphQL queries

Example of a graphql call : 
```json
{
  weather(location: "Casablanca") {
    day
    date
    low
    high
    text 
  }
}
```
Response : 
```json
{
  "data": {
    "weather": {
      "day": "Fri",
      "date": "2019-07-19",
      "low": "69",
      "high": "74",
      "text": "Mostly Sunny"
    }
  }
}
```