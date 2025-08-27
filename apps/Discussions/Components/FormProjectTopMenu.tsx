import React, { Component, createRef } from 'react';
import FormProject, { FormProjectProps, FormProjectState } from '@hubleto/apps/Projects/Components/FormProject'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';
import TableDiscussions from './TableDiscussions';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";

interface P {
  form: FormProject<FormProjectProps, FormProjectState>
}

interface S {
  showDiscussions: boolean;
}

export default class FormProjectTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Projects\\Loader::Components\\FormProject';

  constructor(props: P) {
    super(props);
    this.state = { showDiscussions: false };
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showDiscussions: !this.state.showDiscussions}); }}
        >
          <span className="icon"><i className="fas fa-list-check"></i></span>
          <span className="text">{this.translate('Discussions')}</span>
        </button>
        {this.state.showDiscussions ? <>
          <ModalSimple
            uid='projects_table_discussions_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={<>
              <h2>Discussions</h2>
              <small>Project: {R.title ? R.title : '-'}</small>
            </>}
            onClose={(modal: ModalSimple) => { this.setState({showDiscussions: false}); }}
          >
            <TableDiscussions
              uid={form.props.uid + "_table_discussions"}
              tag={"ProjectDiscussions"}
              parentForm={form}
            />
          </ModalSimple>
        </> : null}
      </>
    }
  }
}

