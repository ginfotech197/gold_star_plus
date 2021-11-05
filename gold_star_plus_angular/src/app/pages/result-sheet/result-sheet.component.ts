import { Component, OnInit } from '@angular/core';
import { CurrentGameResult } from 'src/app/models/CurrentGameResult.model';
import { CommonService } from 'src/app/services/common.service';
import { PlayGameService } from 'src/app/services/play-game.service';
import {TodayLastResult} from '../../models/TodayLastResult.model';


@Component({
  selector: 'app-result-sheet',
  templateUrl: './result-sheet.component.html',
  styleUrls: ['./result-sheet.component.scss']
})
export class ResultSheetComponent implements OnInit {

  todayLastResult: TodayLastResult;
  public currentDateResult: CurrentGameResult;



  constructor(private playGameService: PlayGameService, private commonService: CommonService) {
    this.playGameService.getTodayLastResultListener().subscribe(response => {
      this.todayLastResult = response;
    });
  }

  ngOnInit(): void {

    this.currentDateResult = this.playGameService.getCurrentDateResult();
    this.playGameService.getCurrentDateResultListener().subscribe((response: CurrentGameResult) => {
      this.currentDateResult = response;
      console.log(this.currentDateResult);
    });
  }

}
