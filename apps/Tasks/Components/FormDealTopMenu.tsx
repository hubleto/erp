import React, { Component, createRef } from 'react';
import FormDeal, { FormDealProps, FormDealState } from '@hubleto/apps/Deals/Components/FormDeal'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';
import TableTasks from './TableTasks';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";

interface P {
  form: FormDeal<FormDealProps, FormDealState>
}

interface S {
  tasks?: any;
  showTasks: boolean;
}

export default class FormDealTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: P) {
    super(props);
    this.state = { showTasks: false };
  }

  componentDidMount() {
    request.get(
      'api/record/get-list',
      {
        model: 'HubletoApp\\Community\\Tasks\\Models\\Task',
        externalModel: 'HubletoApp\\Community\\Deals\\Models\\Deal',
        externalId: this.props.form.state.record?.id,
      },
      (tasks: any) => {
        this.setState({tasks: tasks.data});
      }
    );
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showTasks: !this.state.showTasks}); }}
        >
          <span className="icon"><i className="fas fa-list-check"></i></span>
          <span className="text">
            {this.translate('Tasks')}
            {this.state.tasks ? ' (' + this.state.tasks.length + ')' : null}
          </span>
        </button>
        {this.state.showTasks ? <>
          <ModalSimple
            uid='deals_table_tasks_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={<>
              <h2>Tasks</h2>
              <small>Deal: {R.title ? R.title : '-'}</small>
            </>}
            onClose={(modal: ModalSimple) => { this.setState({showTasks: false}); }}
          >
            <TableTasks
              uid={form.props.uid + "_table_tasks"}
              tag={"DealTasks"}
              parentForm={form}
              externalModel='HubletoApp\Community\Deals\Models\Deal'
              externalId={R.id}
            />
          </ModalSimple>
        </> : null}
      </>
    }
  }
}

