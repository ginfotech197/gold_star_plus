import { Component, OnInit } from '@angular/core';
import { CurrentGameResult } from 'src/app/models/CurrentGameResult.model';
import { GameResult } from 'src/app/models/GameResult.model';
import { CommonService } from 'src/app/services/common.service';
import { PlayGameService } from 'src/app/services/play-game.service';
import { ResultService } from 'src/app/services/result.service';
import {TodayLastResult} from '../../models/TodayLastResult.model';
import {DatePipe} from '@angular/common';



@Component({
  selector: 'app-result-sheet',
  templateUrl: './result-sheet.component.html',
  styleUrls: ['./result-sheet.component.scss']
})
export class ResultSheetComponent implements OnInit {

  todayLastResult: TodayLastResult;
  public currentDateResult: any[];
  public resultByDate: GameResult ;
  currentResult: any[] = [];

  thisYear = new Date().getFullYear();
  thisMonth = new Date().getMonth();
  thisDay = new Date().getDate();
  startDate = new Date(this.thisYear, this.thisMonth, this.thisDay);
  pipe = new DatePipe('en-US');




  constructor(private playGameService: PlayGameService, private commonService: CommonService, private resultService: ResultService) {
    this.playGameService.getTodayLastResultListener().subscribe(response => {
      this.todayLastResult = response;
    });
    this.searchResultByDate();
  }

  ngOnInit(): void {

    // this.currentDateResult = this.playGameService.getCurrentDateResult();
    // this.playGameService.getCurrentDateResultListener().subscribe((response: CurrentGameResult) => {
    //   // @ts-ignore
    //   this.currentDateResult = response.result;
    //   console.log("ResultSheetComponent", this.currentDateResult);
    // });
    // console.log("ResultSheetComponent", this.currentDateResult);


    this.resultService.getResultByDate('2021-11-20').subscribe(response=>{
      // console.log('Component',response);
    });
    this.resultService.getResultByDateListener().subscribe((response: GameResult) => {
      this.resultByDate = response;
      // console.log(response);
    });
  }

  searchResultByDate(){
    let x = this.pipe.transform(this.startDate,'yyyy-MM-dd');
    // console.log(this.startDate);
    // console.log(x);
    this.resultService.getResultByDate(x).subscribe(response=>{
      // console.log('Component',response);
     // @ts-ignore

      this.currentResult = response.data;
      // console.log(this.currentResult);
    });

  }


}
