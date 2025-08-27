import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Messages from './Messages';
import TableMessages from './TableMessages';
import TableMembers from './TableMembers';
import TableSpeakers from '../../Events/Components/TableSpeakers';

interface FormDiscussionProps extends HubletoFormProps { }
interface FormDiscussionState extends HubletoFormState { }

export default class FormDiscussion<P, S> extends HubletoForm<FormDiscussionProps, FormDiscussionState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Discussions/Models/Discussion',
  }

  props: FormDiscussionProps;
  state: FormDiscussionState;

  translationContext: string = 'HubletoApp\\Community\\Discussions::Components\\FormDiscussion';

  constructor(props: FormDiscussionProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDiscussionProps) {
    let tabs = {};
    if (this.props.id < 0) {
      tabs = [
        { uid: 'default', title: this.translate('About') }
      ];
    } else {
      tabs = [
        { uid: 'default', title: this.translate('Messages') },
        { uid: 'about', title: this.translate('About') },
      ];
    }

    return {
      ...super.getStateFromProps(props),
      tabs: tabs,
    } as FormDiscussionState;
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Discussion</small>
      <h2>{this.state.record.topic ?? '-'}</h2>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    const tabAbout = (
      <div className='w-full flex gap-2 flex-col md:flex-row'>
        <div className='flex-1 border-r border-gray-100'>
          {this.inputWrapper('topic')}
          {this.inputWrapper('description')}
          {this.inputWrapper('id_main_mod')}
          {this.inputWrapper('is_closed')}
        </div>
        <div className='flex-1'>
          {this.inputWrapper('notes')}
          {this.inputWrapper('date_created')}
        </div>
      </div>
    );

    const tabMessages = (
      <Messages
        uid={this.props.uid + "_table_messages"}
        tag="DiscussionMessages"
        parentForm={this}
        idDiscussion={R.id}
      />
    );


    switch (tab) {
      case 'default': return this.state.id < 0 ? tabAbout : tabMessages; break;
      case 'about': return this.state.id < 0 ? null : tabAbout; break;
    }
  }

}
