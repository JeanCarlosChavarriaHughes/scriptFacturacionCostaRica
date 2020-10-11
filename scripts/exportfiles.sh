#!/bin/bash

input="/Users/jchavarr/Documents/local/scriptFacturacionCostaRica/.env"

while IFS= read -r line
do
  echo $line
  export $line
done < "$input"
