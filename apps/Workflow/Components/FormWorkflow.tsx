import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "@hubleto/react-ui/core/Helper";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableWorkflowSteps from "./TableWorkflowSteps";
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";

interface FormWorkflowProps extends HubletoFormProps {}

interface FormWorkflowState extends HubletoFormState {
  tablesKey: number,
}

export default class FormWorkflow<P, S> extends HubletoForm<FormWorkflowProps, FormWorkflowState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "Hubleto/App/Community/Workflow/Models/Workflow",
  };

  props: FormWorkflowProps;
  state: FormWorkflowState;

  translationContext: string = "Hubleto\\App\\Community\\Workflow\\Loader::Components\\FormWorkflow";

  constructor(props: FormWorkflowProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      tablesKey: 0,
    };
  }

  getStateFromProps(props: FormWorkflowProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['STEPS'],
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Workflow</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormWorkflowState)
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div
          className="grid grid-cols-2 gap-1"
          style={{
            gridTemplateAreas: `
            'info info'
            'steps steps'
          `,
          }}
        >
          <div className="card mt-4" style={{ gridArea: "info" }}>
            <div className="card-header">Workflow Information</div>
            <div className="card-body flex flex-row justify-around">
              {this.inputWrapper("name")}
              {this.inputWrapper("group")}
              {this.inputWrapper("description")}
            </div>
          </div>

          <div className="card mt-4" style={{ gridArea: "steps" }}>
            <div className="card-header">Workflow Steps</div>
            <div className="card-body">

              <a
                className="btn btn-add-outline mb-2"
                onClick={() => {
                  if (!R.STEPS) R.STEPS = [];
                  R.STEPS.push({
                    id_workflow: { _useMasterRecordId_: true },
                  });
                  this.setState({ record: R, isInlineEditing: true});
                }}
              >
                <span className="icon"><i className="fas fa-add"></i></span>
                <span className="text">Add step</span>
              </a>

              <TableWorkflowSteps
                key={this.state.tablesKey}
                uid={this.props.uid + "_table_workflow_steps_input"}
                context="Hello World"
                descriptionSource="props"
                data={{ data: R.STEPS }}
                isUsedAsInput={true}
                isInlineEditing={this.state.isInlineEditing}
                onRowClick={() => this.setState({isInlineEditing: true})}
                onChange={(table: TableWorkflowSteps) => {
                  this.updateRecord({ STEPS: table.state.data?.data });
                }}
                description={{
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  permissions: {
                    canCreate: true,
                    canDelete: true,
                    canRead: true,
                    canUpdate: true,
                  },
                  columns: {
                    name: { type: "varchar", title: "Name" },
                    order: { type: "int", title: "Order" },
                    color: { type: "color", title: "Color" },
                    probability: { type: "int", title: "Probability", unit: "%" },
                    tag: { type: "varchar", title: "Tag"},
                    set_result: { type: "integer", title: "Sets result of a deal to", enumValues: {1: "Pending", 2: "Won", 3: "Lost"} },
                  },
                  inputs: {
                    name: { type: "varchar", title: "Name" },
                    order: { type: "int", title: "Order" },
                    color: { type: "color", title: "Color" },
                    probability: { type: "int", title: "Probability", unit: "%" },
                    tag: { type: "varchar", title: "Tag"},
                    set_result: { type: "integer", title: "Sets result of a deal to", enumValues: {1: "Pending", 2: "Won", 3: "Lost"} },
                  },
                }}
              ></TableWorkflowSteps>
            </div>
          </div>
        </div>
      </>
    );
  }
}
