import { Component, OnInit } from '@angular/core';
import { CurrentGameResult } from 'src/app/models/CurrentGameResult.model';
import { GameResult } from 'src/app/models/GameResult.model';
import { CommonService } from 'src/app/services/common.service';
import { PlayGameService } from 'src/app/services/play-game.service';
import { ResultService } from 'src/app/services/result.service';
import {TodayLastResult} from '../../models/TodayLastResult.model';


@Component({
  selector: 'app-result-sheet',
  templateUrl: './result-sheet.component.html',
  styleUrls: ['./result-sheet.component.scss']
})
export class ResultSheetComponent implements OnInit {

  todayLastResult: TodayLastResult;
  public currentDateResult: CurrentGameResult;
  public resultByDate: GameResult ;



  constructor(private playGameService: PlayGameService, private commonService: CommonService, private resultService: ResultService) {
    this.playGameService.getTodayLastResultListener().subscribe(response => {
      this.todayLastResult = response;
    });
  }

  ngOnInit(): void {

    this.currentDateResult = this.playGameService.getCurrentDateResult();
    this.playGameService.getCurrentDateResultListener().subscribe((response: CurrentGameResult) => {
      this.currentDateResult = response;
      // console.log("ResultSheetComponent", this.currentDateResult);
    });

    this.resultService.getResultByDate('2021-11-08').subscribe(response=>{
      console.log(response);
    });
    this.resultService.getResultByDateListener().subscribe((response: GameResult) => {
      this.resultByDate = response;
      console.log(response);
    });
  }

  

}
