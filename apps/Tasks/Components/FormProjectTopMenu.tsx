import React, { Component, createRef } from 'react';
import FormProject, { FormProjectProps, FormProjectState } from '@hubleto/apps/Projects/Components/FormProject'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';
import TableTasks from './TableTasks';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";

interface P {
  form: FormProject<FormProjectProps, FormProjectState>
}

interface S {
  showTasks: boolean;
}

export default class FormProjectTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Projects\\Loader::Components\\FormProject';

  constructor(props: P) {
    super(props);
    this.state = { showTasks: false };
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
          <span className="text">{this.translate('Tasks')}</span>
        </button>
        {this.state.showTasks ? <>
          <ModalSimple
            uid='projects_table_tasks_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={<>
              <h2>Tasks</h2>
              <small>Project: {R.title ? R.title : '-'}</small>
            </>}
            onClose={(modal: ModalSimple) => { this.setState({showTasks: false}); }}
          >
            <TableTasks
              uid={form.props.uid + "_table_tasks"}
              tag={"ProjectTasks"}
              parentForm={form}
              externalModel='HubletoApp\Community\Projects\Models\Project'
              externalId={R.id}
            />
          </ModalSimple>
        </> : null}
      </>
    }
  }
}

