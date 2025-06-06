import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import Lookup from 'adios/Inputs/Lookup';
import FormInput from 'adios/FormInput';

export interface FormActivityProps extends HubletoFormProps {}

export interface FormActivityState extends HubletoFormState {}

export default class FormActivity<P, S> extends HubletoForm<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Calendar/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormActivity';

  renderTitle(): JSX.Element {
    if (this.state.creatingRecord) {
      return <h2>{globalThis.main.translate('New event')}</h2>;
    } else {
      return (
        <>
          <h2>{this.state.record.subject ?? ''}</h2>
          <small>Event</small>
        </>
      );
    }
  }

  renderCustomInputs(): JSX.Element {
    return null;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional: boolean = R.id > 0 ? true : false;

    const customInputs = this.renderCustomInputs();

    return <>
      {customInputs ? <div className="p-2 mb-2 bg-blue-50">{customInputs}</div> : null}

      {this.inputWrapper('subject', {cssClass: 'text-primary text-2xl'})}
      {this.inputWrapper('id_activity_type')}
      <div className='flex gap-2 w-full'>
        <div className='w-full'>
          {this.inputWrapper('all_day')}
        </div>
        <div className='w-full'>
          {this.inputWrapper('completed')}
        </div>
      </div>
      <div className='flex gap-2 w-full'>
        <div>
          {this.divider('Start')}
          {this.input('date_start')}
          {R.all_day ? null : this.input('time_start')}
        </div>
        <div>
          {this.divider('End')}
          {this.input('date_end')}
          {R.all_day ? null : this.input('time_end')}
        </div>
      </div>
      {this.inputWrapper('id_owner', {readonly: true, value: globalThis.main.idUser})}
    </>;
  }
}
