import { Component } from '@angular/core';
import { Credentials } from './credentials';
import {Title} from "@angular/platform-browser";
import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  responsedata = '';
  
  //one way binding
  options = ['Lose Weight', 'Gain Weight'];
  //two way binding
  credentialsModel = new Credentials('', '', '', '');

  //set title and also httpclient
  constructor(private titleService:Title, private http: HttpClient) {
    this.titleService.setTitle("Weight, What?");
  }


  senddata(data){
  	console.log(data);
  	let params = JSON.stringify(data);
  	this.http.post('http://192.168.64.2/project/ngphp-post.php', data)
  	.subscribe((data) => {
  		console.log('Got data from from backend', data);
  		this.responsedata = data;
      if (data == 'received'){
        alert('Account Created Successfully');
        window.location.href = 'http://192.168.64.2/project/login.php';
      }
  	}, (error) => {
  		console.log('Error', error);
  	})
  }
}
