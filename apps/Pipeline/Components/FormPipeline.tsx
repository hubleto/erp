import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "@hubleto/react-ui/core/Helper";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TablePipelineSteps from "./TablePipelineSteps";
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";

interface FormPipelineProps extends HubletoFormProps {}

interface FormPipelineState extends HubletoFormState {
  tablesKey: number,
}

export default class FormPipeline<P, S> extends HubletoForm<FormPipelineProps, FormPipelineState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "HubletoApp/Community/Pipeline/Models/Pipeline",
  };

  props: FormPipelineProps;
  state: FormPipelineState;

  translationContext: string = "HubletoApp\\Community\\Pipeline\\Loader::Components\\FormPipeline";

  constructor(props: FormPipelineProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      tablesKey: 0,
    };
  }

  getStateFromProps(props: FormPipelineProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam("recordId") == -1) {
      return (
        <>
          <h2>{"New Pipeline"}</h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ? this.state.record.name
              : "[Undefined Name]"}
          </h2>
        </>
      );
    }
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormPipelineState)
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
            <div className="card-header">Pipeline Information</div>
            <div className="card-body flex flex-row justify-around">
              {this.inputWrapper("name")}
              {this.inputWrapper("type")}
              {this.inputWrapper("description")}
            </div>
          </div>

          <div className="card mt-4" style={{ gridArea: "steps" }}>
            <div className="card-header">Pipeline Steps</div>
            <div className="card-body">

              <a
                className="btn btn-add-outline mb-2"
                onClick={() => {
                  if (!R.STEPS) R.STEPS = [];
                  R.STEPS.push({
                    id_pipeline: { _useMasterRecordId_: true },
                  });
                  this.setState({ record: R, isInlineEditing: true});
                }}
              >
                <span className="icon"><i className="fas fa-add"></i></span>
                <span className="text">Add step</span>
              </a>

              <TablePipelineSteps
                key={this.state.tablesKey}
                uid={this.props.uid + "_table_pipeline_steps_input"}
                context="Hello World"
                descriptionSource="props"
                data={{ data: R.STEPS }}
                isUsedAsInput={true}
                isInlineEditing={this.state.isInlineEditing}
                onRowClick={() => this.setState({isInlineEditing: true})}
                onChange={(table: TablePipelineSteps) => {
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
                    set_result: { type: "integer", title: "Sets result of a deal to", enumValues: {1: "Pending", 2: "Won", 3: "Lost"} },
                  },
                  inputs: {
                    name: { type: "varchar", title: "Name" },
                    order: { type: "int", title: "Order" },
                    color: { type: "color", title: "Color" },
                    probability: { type: "int", title: "Probability", unit: "%" },
                    set_result: { type: "integer", title: "Sets result of a deal to", enumValues: {1: "Pending", 2: "Won", 3: "Lost"} },
                  },
                }}
              ></TablePipelineSteps>
            </div>
          </div>
        </div>
      </>
    );
  }
}
