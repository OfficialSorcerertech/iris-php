# iris-php
Iris is being updated to be even better. We wanted to give back to the community and provide our old code for everyone to use. Works (ish) on PHP 7. **_Pull requests welcome!_**

## License
[The Sorcerertech Open Web License](https://github.com/OfficialSorcerertech/sorcerertech-open-licenses/tree/master/open-web)

## How it works
Badly. This has fallen into disrepair, so we'll be re-writing this. You should consider this more of a base for your own project.



Use a GET request to open iris.php as follows:

```.../iris.php?r={user request}&location={location string}```

`{user request}` is a URL encoded form of the question asked by the user.

`{location string}` is the location of the user in a urlencoded string resembling:

```latitude,longitude,city,countryCode,methodOfFindingLocation");```

(`methodOfFindingLocation` should be `ip` if an IP address was used to gain location data)
