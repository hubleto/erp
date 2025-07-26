import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import moment from 'moment';

export interface FormSharedCalendarProps extends HubletoFormProps {
  onUpdate: () => void;
}

export interface FormSharedCalendarState extends HubletoFormState {}

export default class FormSharedCalendar<P, S> extends HubletoForm<FormSharedCalendarProps,FormSharedCalendarState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Calendar/Models/SharedCalendar',
  };

  props: FormSharedCalendarProps;
  state: FormSharedCalendarState;

  translationContext: string = 'HubletoApp\\Community\\Calendar\\Loader::Components\\FormSharedCalendar';

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ?? ''}</h2>
      <small>Shared calendar</small>
    </>;
  }

  onAfterSaveRecord(saveResponse: any) {
    super.onAfterSaveRecord(saveResponse);
    this.props.onUpdate();
  }

  onAfterDeleteRecord(deleteResponse: any) {
    super.onAfterDeleteRecord(deleteResponse);
    this.props.onUpdate();
  }

  renderContent(): JSX.Element {

    return <>
      {this.inputWrapper("id_owner")}
      {this.inputWrapper("calendar")}
      {this.inputWrapper("view_details")}
      {this.inputWrapper("enabled")}
      <div className="flex align-middle justify-between">
        <div>
          {this.inputWrapper("date_from")}
        </div>
        <div>
          {this.inputWrapper("date_to")}
        </div>
      </div>

      { this.state.record.id > 0 &&
        <div className="flex gap-2 mt-4 input-element">
          <input type="text" className={"flex-grow bg-muted"} value={globalThis.app.config.rootUrl + "/calendar/" + this.state.record.share_key + "/ics"} readOnly={true}/>
          <button className="btn btn-primary px-2" onClick={() => navigator.clipboard.writeText(globalThis.app.config.rootUrl + "/calendar/" + this.state.record.share_key + "/ics")}>
            Copy share link
          </button>
        </div>
      }

    </>;
  }
}
