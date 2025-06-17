import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface ListFolderProps extends HubletoFormProps {}

export interface ListFolderState extends HubletoFormState {}

export default class ListFolder<P, S> extends HubletoForm<ListFolderProps,ListFolderState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Messages/Models/Message',
  };

  props: ListFolderProps;
  state: ListFolderState;

  translationContext: string = 'HubletoApp\\Community\\Messages\\Loader::Components\\FormMessage';

  constructor(props: ListFolderProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: ListFolderProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>Message</small>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('to')}
          {this.inputWrapper('cc')}
          {this.inputWrapper('bcc')}
          {this.inputWrapper('subject')}
          {this.inputWrapper('body')}
        </div>
        <div className='flex-1'>
          {/* {this.inputWrapper('id_owner')} */}
          {this.inputWrapper('from')}
          {this.inputWrapper('priority')}
          {this.inputWrapper('sent')}
          {this.inputWrapper('color')}
        </div>
      </div>
    </>;
  }
}

