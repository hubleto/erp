import React, { Component, createRef } from 'react';
import FormDeal, { FormDealProps, FormDealState } from '@hubleto/apps/Deals/Components/FormDeal'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';
import TableProjects from './TableProjects';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";

interface P {
  form: FormDeal<FormDealProps, FormDealState>
}

interface S {
  projects?: any,
  showProjects: boolean;
  showIdProject: number,
}

export default class FormDealTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Projects\\Loader::Components\\FormProject';

  refTableProjects: any;

  constructor(props: P) {
    super(props);
    this.refTableProjects = createRef();
    this.state = { showProjects: false, showIdProject: 0 };
  }

  componentDidMount() {
    request.get(
      'api/record/get-list',
      {
        model: 'HubletoApp\\Community\\Projects\\Models\\Project',
        idDeal: this.props.form.state.record?.id,
      },
      (projects: any) => {
        this.setState({projects: projects.data});
      }
    );
  }

  convertToProject(idDeal: number) {
    request.get(
      'projects/api/convert-deal-to-project',
      {idDeal: idDeal},
      (data: any) => {
        if (data.status == "success") {
          // globalThis.main.lastShownDialogRef.current.hide();
          console.log(this.refTableProjects, this.refTableProjects.current);
          this.refTableProjects.current.reload();//setState({recordId: data.idProject});
        }
      }
    );
  }

  confirmConvertToProject(idDeal: number) {
    globalThis.main.showDialogDanger(
      'Are you sure you want to convert this deal to a project?',
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to project",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertToProject(idDeal)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to project</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => { globalThis.main.lastShownDialogRef.current.hide(); }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert</span>
          </button>
        </>
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
          onClick={() => { this.setState({showProjects: !this.state.showProjects}); }}
        >
          <span className="icon"><i className="fas fa-handshake"></i></span>
          <span className="text">
            {this.translate('Projects')}
            {this.state.projects ? ' (' + this.state.projects.length + ')' : null}
          </span>
        </button>
        {this.state.showProjects ? <>
          <ModalSimple
            uid='deals_table_projects_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={<>
              <h2>Projects</h2>
              <small>Deal: {R.title ? R.title : '-'}</small>
            </>}
            topMenu={
              <a className='btn btn-transparent' onClick={() => this.convertToProject(R.id)}>
                <span className='icon'><i className='fas fa-rotate-right'></i></span>
                <span className='text'>Convert deal to project</span>
              </a>
            }
            onClose={(modal: ModalSimple) => { this.setState({showProjects: false}); }}
          >
            <TableProjects
              ref={this.refTableProjects}
              uid={form.props.uid + "_table_projects"}
              tag={"DealProjects"}
              parentForm={form}
              idDeal={R.id}
              recordId={this.state.showIdProject}
              descriptionSource='both'
              // description={{permissions: {canCreate: false}}}
            />
          </ModalSimple>
        </> : null}
      </>
    }
  }
}

